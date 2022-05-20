<?php

namespace InstagramAPI;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Utils as GuzzleUtils;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Exception\LoginRequiredException;
use InstagramAPI\Exception\ServerMessageThrower;
use InstagramAPI\Middleware\FakeCookies;
use InstagramAPI\Middleware\ZeroRating;
use LazyJsonMapper\Exception\LazyJsonMapperException;
use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

/**
 * This class handles core API network communication.
 */
class Client
{
    /**
     * How frequently we're allowed to auto-save the cookie jar, in seconds.
     *
     * @var int
     */
    const COOKIE_AUTOSAVE_INTERVAL = 45;

    /**
     * The Instagram class instance we belong to.
     *
     * @var \InstagramAPI\Instagram
     */
    protected $_parent;

    /**
     * What user agent to identify our client as.
     *
     * @var string
     */
    protected $_userAgent;

    /**
     * The SSL certificate verification behavior of requests.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#verify
     *
     * @var bool|string
     */
    protected $_verifySSL;

    /**
     * Proxy to use for all requests. Optional.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#proxy
     *
     * @var string|array|null
     */
    protected $_proxy;

    /**
     * Network interface override to use.
     *
     * Only works if Guzzle is using the cURL backend. But that's
     * almost always the case, on most PHP installations.
     *
     * @see http://php.net/curl_setopt CURLOPT_INTERFACE
     *
     * @var string|null
     */
    protected $_outputInterface;

    /**
     * @var \GuzzleHttp\Client
     */
    private $_guzzleClient;

    /**
     * @var \InstagramAPI\Middleware\FakeCookies
     */
    private $_fakeCookies;

    /**
     * @var \InstagramAPI\Middleware\ZeroRating
     */
    private $_zeroRating;

    /**
     * @var \GuzzleHttp\Cookie\CookieJar
     */
    private $_cookieJar;

    /**
     * The timestamp of when we last saved our cookie jar to disk.
     *
     * Used for automatically saving the jar after any API call, after enough
     * time has elapsed since our last save.
     *
     * @var int
     */
    private $_cookieJarLastSaved;

    /**
     * The flag to force cURL to reopen a fresh connection.
     *
     * @var bool
     */
    private $_resetConnection;

    /**
     * The most recent request processed.
     *
     * Used for debugging failed requests in exceptions without needing to
     * enable debug mode.
     *
     * @var Request
     */
    private $_lastRequest;

    /**
     * The flag will use same pigeon Timestamp.
     *
     * @var bool
     */
    private $_pigeonBatch;

    /**
     * The Pigeon Timestamp.
     *
     * @var float
     */
    private $_pigeonTimestamp;

    /**
     * The Pigeon Session ID.
     *
     * @var string
     */
    private $_pigeonSession;

    /**
     * Additional cookies for request header
     *
     * @var string
     */
    public $_authorization = '';
    public $_wwwClaim = '';
    public $_DIRECT_REGION_HINT = '';
    public $_SHBID = '';
    public $_SHBTS = '';
    public $_RUR = '';
    public $_DS_USER_ID = '';

    /**
     * Constructor.
     *
     * @param \InstagramAPI\Instagram $parent
     */
    public function __construct(
        $parent)
    {
        $this->_parent = $parent;

        // Defaults.
        $this->_verifySSL = true;
        $this->_proxy = null;

        // Set Pigeon Session ID.
        $this->_pigeonSession = 'UFS-' . Signatures::generateUUID();

        // Create a default handler stack with Guzzle's auto-selected "best
        // possible transfer handler for the user's system", and with all of
        // Guzzle's default middleware (cookie jar support, etc).
        $stack = HandlerStack::create();

        // Create our cookies middleware and add it to the stack.
        $this->_fakeCookies = new FakeCookies();
        $stack->push($this->_fakeCookies, 'fake_cookies');

        $this->_zeroRating = new ZeroRating($parent);
        $stack->push($this->_zeroRating, 'zero_rewrite');

        // Default request options (immutable after client creation).
        $this->_guzzleClient = new GuzzleClient([
            'handler'         => $stack, // Our middleware is now injected.
            'allow_redirects' => [
                'max' => 8, // Allow up to eight redirects (that's plenty).
            ],
            'connect_timeout' => 30.0, // Give up trying to connect after 30s.
            'decode_content'  => true, // Decode gzip/deflate/etc HTTP responses.
            'timeout'         => 240.0, // Maximum per-request time (seconds).
            // Tells Guzzle to stop throwing exceptions on non-"2xx" HTTP codes,
            // thus ensuring that it only triggers exceptions on socket errors!
            // We'll instead MANUALLY be throwing on certain other HTTP codes.
            'http_errors'     => false,
        ]);

        $this->_resetConnection = false;
    }

    /**
     * Resets certain Client settings via the current Settings storage.
     *
     * Used whenever we switch active user, to configure our internal state.
     *
     * @param bool $resetCookieJar (optional) Whether to clear current cookies.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function updateFromCurrentSettings(
        $resetCookieJar = false)
    {
        // Update our internal client state from the new user's settings.
        $this->_userAgent = $this->_parent->device->getUserAgent();
        $this->loadCookieJar($resetCookieJar);

        // Verify that the jar contains a non-expired csrftoken for the API
        // domain. Instagram gives us a 1-year csrftoken whenever we log in.
        // If it's missing, we're definitely NOT logged in! But even if all of
        // these checks succeed, the cookie may still not be valid. It's just a
        // preliminary check to detect definitely-invalid session cookies!
        if ($this->getToken() === null) {
            $this->_parent->isMaybeLoggedIn = false;
        }

        // Load rewrite rules (if any).
        $this->zeroRating()->update($this->_parent->settings->getRewriteRules());
    }

    /**
     * Loads all cookies via the current Settings storage.
     *
     * @param bool $resetCookieJar (optional) Whether to clear current cookies.
     *
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function loadCookieJar(
        $resetCookieJar = false)
    {
        // Mark any previous cookie jar for garbage collection.
        $this->_cookieJar = null;

        // Delete all current cookies from the storage if this is a reset.
        if ($resetCookieJar) {
            $this->_parent->settings->setCookies('');
        }

        // Get all cookies for the currently active user.
        $cookieData = $this->_parent->settings->getCookies();

        // Attempt to restore the cookies, otherwise create a new, empty jar.
        $restoredCookies = is_string($cookieData) ? @json_decode($cookieData, true) : null;
        if (!is_array($restoredCookies)) {
            $restoredCookies = [];
        }

        // Memory-based cookie jar which must be manually saved later.
        $this->_cookieJar = new CookieJar(false, $restoredCookies);

        // Reset the "last saved" timestamp to the current time to prevent
        // auto-saving the cookies again immediately after this jar is loaded.
        $this->_cookieJarLastSaved = time();
    }

    /**
     * Retrieve Pigeon Session ID.
     *
     * @return string
     */
    public function getPigeonSession()
    {
        return $this->_pigeonSession;
    }

    /**
     * Retrieve the CSRF token from the current cookie jar.
     *
     * Note that Instagram gives you a 1-year token expiration timestamp when
     * you log in. But if you log out, they set its timestamp to "0" which means
     * that the cookie is "expired" and invalid. We ignore token cookies if they
     * have been logged out, or if they have expired naturally.
     *
     * @return string|null The token if found and non-expired, otherwise NULL.
     */
    public function getToken()
    {
        $cookie = $this->getCookie('csrftoken', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Retrieve the MID token from the current cookie jar.
     *
     * @return string|null The MID if found and non-expired, otherwise NULL.
     */
    public function getMid()
    {
        $cookie = $this->getCookie('mid', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Retrieve the ds_user_id token from the current cookie jar.
     *
     * @return string|null The ds_user_id if found and non-expired, otherwise NULL.
     */
    public function getDSUserId()
    {
        $cookie = $this->getCookie('ds_user_id', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Retrieve the SessionID token from the current cookie jar.
     *
     * @return string|null The SessionID if found and non-expired, otherwise NULL.
     */
    public function getSessionID()
    {
        $cookie = $this->getCookie('sessionid', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Retrieve the urlgen token from the current cookie jar.
     *
     * @return string|null The urlgen if found and non-expired, otherwise NULL.
     */
    public function getURLGen()
    {
        $cookie = $this->getCookie('urlgen', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Retrieve the shbid token from the current cookie jar.
     *
     * @return string|null The shbid if found and non-expired, otherwise NULL.
     */
    public function getShbid()
    {
        $cookie = $this->getCookie('shbid', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Retrieve the shbts token from the current cookie jar.
     *
     * @return string|null The shbts if found and non-expired, otherwise NULL.
     */
    public function getShbts()
    {
        $cookie = $this->getCookie('shbts', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Retrieve the rur token from the current cookie jar.
     *
     * @return string|null The rur if found and non-expired, otherwise NULL.
     */
    public function getRUR()
    {
        $cookie = $this->getCookie('rur', 'i.instagram.com');
        if ($cookie === null || $cookie->getValue() === '') {
            return null;
        }

        return $cookie->getValue();
    }

    /**
     * Searches for a specific cookie in the current jar.
     *
     * @param string      $name   The name of the cookie.
     * @param string|null $domain (optional) Require a specific domain match.
     * @param string|null $path   (optional) Require a specific path match.
     *
     * @return \GuzzleHttp\Cookie\SetCookie|null A cookie if found and non-expired, otherwise NULL.
     */
    public function getCookie(
        $name,
        $domain = null,
        $path = null)
    {
        $foundCookie = null;
        if ($this->_cookieJar instanceof CookieJar) {
            /** @var SetCookie $cookie */
            foreach ($this->_cookieJar->getIterator() as $cookie) {
                if ($cookie->getName() === $name
                    && !$cookie->isExpired()
                    && ($domain === null || $cookie->matchesDomain($domain))
                    && ($path === null || $cookie->matchesPath($path))) {
                    // Loop-"break" is omitted intentionally, because we might
                    // have more than one cookie with the same name, so we will
                    // return the LAST one. This is necessary because Instagram
                    // has changed their cookie domain from `i.instagram.com` to
                    // `.instagram.com` and we want the *most recent* cookie.
                    // Guzzle's `CookieJar::setCookie()` always places the most
                    // recently added/modified cookies at the *end* of array.
                    $foundCookie = $cookie;
                }
            }
        }

        return $foundCookie;
    }

    /**
     * Set a cookie in the current jar.
     *
     * @param string      $name   The name of the cookie.
     * @param string|null $domain (optional) Require a specific domain match.
     * @param string|null $path   (optional) Require a specific path match.
     *
     * @return self
     */
    public function setCookie(
        $name,
        $domain = null,
        $value = null)
    {
        $cookie = new SetCookie();
        $cookie->setName($name);
        $cookie->setValue($value);
        $cookie->setDomain($domain);

        $this->_cookieJar->setCookie($cookie);

        return $this;
    }

    /**
     * Remove a cookie from the current jar.
     *
     * @param string      $name   The name of the cookie.
     *
     * @return self
     */
    public function removeCookie(
        $name)
    {
        foreach ($this->_cookieJar->getIterator() as $cookie) {
            if ($cookie->getName() == $name) {
                $this->_cookieJar->clear(
                    $cookie->getDomain(),
                    $cookie->getPath(),
                    $cookie->getName()
                );

                break;
            }
        }

        return $this;
    }

    /**
     * Return a cookie from the current jar.
     *
     * @param string      $name   The name of the cookie.
     *
     * @return self
     */
    public function returnCookie(
        $name)
    {
        foreach ($this->_cookieJar->getIterator() as $cookie) {
            if ($cookie->getName() == $name) {
                return $cookie->getValue();
            }
        }

        return false;
    }

    /**
     * Remove empty cookies from current jar.
     *
     * @return self
     */
    public function removeEmptyCookies() {
        if ($this->_cookieJar) {
            foreach ($this->_cookieJar->getIterator() as $cookie) {
                $name = $cookie->getName();
                $value = $cookie->getValue();
                if ($cookie->getValue() == '""') {
                    $this->_cookieJar->clear(
                        $cookie->getDomain(),
                        $cookie->getPath(),
                        $cookie->getName()
                    );

                    break;
                }
            }
        }

        return $this;
    }
    
    /**
     * Gives you all cookies in the Jar encoded as a JSON string.
     *
     * This allows custom Settings storages to retrieve all cookies for saving.
     *
     * @throws \InvalidArgumentException If the JSON cannot be encoded.
     *
     * @return string
     */
    public function getCookieJarAsJSON()
    {
        if (!$this->_cookieJar instanceof CookieJar) {
            return '[]';
        }

        // Gets ALL cookies from the jar, even temporary session-based cookies.
        $cookies = $this->_cookieJar->toArray();

        // Throws if data can't be encoded as JSON (will never happen).
        $jsonStr = \GuzzleHttp\json_encode($cookies);

        return $jsonStr;
    }

    /**
     * Tells current settings storage to store cookies if necessary.
     *
     * NOTE: This Client class is NOT responsible for calling this function!
     * Instead, our parent "Instagram" instance takes care of it and saves the
     * cookies "onCloseUser", so that cookies are written to storage in a
     * single, efficient write when the user's session is finished. We also call
     * it during some important function calls such as login/logout. Client also
     * automatically calls it when enough time has elapsed since last save.
     *
     * @throws \InvalidArgumentException                 If the JSON cannot be encoded.
     * @throws \InstagramAPI\Exception\SettingsException
     */
    public function saveCookieJar()
    {
        // Tell the settings storage to persist the latest cookies.
        $newCookies = $this->getCookieJarAsJSON();
        $this->_parent->settings->setCookies($newCookies);

        // Reset the "last saved" timestamp to the current time.
        $this->_cookieJarLastSaved = time();
    }

    /**
     * Controls the SSL verification behavior of the Client.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#verify
     *
     * @param bool|string $state TRUE to verify using PHP's default CA bundle,
     *                           FALSE to disable SSL verification (this is
     *                           insecure!), String to verify using this path to
     *                           a custom CA bundle file.
     */
    public function setVerifySSL(
        $state)
    {
        $this->_verifySSL = $state;
    }

    /**
     * Gets the current SSL verification behavior of the Client.
     *
     * @return bool|string
     */
    public function getVerifySSL()
    {
        return $this->_verifySSL;
    }

    /**
     * Set the proxy to use for requests.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#proxy
     *
     * @param string|array|null $value String or Array specifying a proxy in
     *                                 Guzzle format, or NULL to disable proxying.
     */
    public function setProxy(
        $value)
    {
        $proxy_parts = explode('://', $value);
        if ($proxy_parts[0] == "https" || $proxy_parts[0] == "http") {
            $value = $proxy_parts[1];
        }
        $this->_proxy = $value;
        $this->_resetConnection = true;
    }

    /**
     * Gets the current proxy used for requests.
     *
     * @return string|array|null
     */
    public function getProxy()
    {
        return $this->_proxy;
    }

    /**
     * Sets the network interface override to use.
     *
     * Only works if Guzzle is using the cURL backend. But that's
     * almost always the case, on most PHP installations.
     *
     * @see http://php.net/curl_setopt CURLOPT_INTERFACE
     *
     * @param string|null $value Interface name, IP address or hostname, or NULL to
     *                           disable override and let Guzzle use any interface.
     */
    public function setOutputInterface(
        $value)
    {
        $this->_outputInterface = $value;
        $this->_resetConnection = true;
    }

    /**
     * Gets the current network interface override used for requests.
     *
     * @return string|null
     */
    public function getOutputInterface()
    {
        return $this->_outputInterface;
    }

    /**
     * Output debugging information.
     *
     * @param string                $method        "GET" or "POST".
     * @param string                $url           The URL or endpoint used for the request.
     * @param string|null           $uploadedBody  What was sent to the server. Use NULL to
     *                                             avoid displaying it.
     * @param int|null              $uploadedBytes How many bytes were uploaded. Use NULL to
     *                                             avoid displaying it.
     * @param HttpResponseInterface $response      The Guzzle response object from the request.
     * @param string                $responseBody  The actual text-body reply from the server.
     * @param bool                  $debug         CLI Debug.
     */
    protected function _printDebug(
        $method,
        $url,
        $uploadedBody,
        $uploadedBytes,
        HttpResponseInterface $response,
        $responseBody,
        $debug)
    {
        $path = Debug::$debugLogPath;
        if ($this->_parent->settings->getStorage() instanceof \InstagramAPI\Settings\Storage\File) {
            $path = $this->_parent->settings->getUserPath($this->_parent->username);
        }
        Debug::printRequest($method, $url, $path, $debug);

        // Display the data body that was uploaded, if provided for debugging.
        // NOTE: Only provide this from functions that submit meaningful BODY data!
        if (is_string($uploadedBody)) {
            Debug::printPostData($uploadedBody, $path, $debug);
        }

        // Display the number of bytes uploaded in the data body, if provided for debugging.
        // NOTE: Only provide this from functions that actually upload files!
        if ($uploadedBytes !== null) {
            Debug::printUpload(Utils::formatBytes($uploadedBytes), $path, $debug);
        }

        // Display the number of bytes received from the response, and status code.
        if ($response->hasHeader('x-encoded-content-length')) {
            $bytes = Utils::formatBytes((int) $response->getHeaderLine('x-encoded-content-length'));
        } elseif ($response->hasHeader('Content-Length')) {
            $bytes = Utils::formatBytes((int) $response->getHeaderLine('Content-Length'));
        } else {
            $bytes = 0;
        }
        Debug::printHttpCode($response->getStatusCode(), $bytes, $path, $debug);

        // Display the actual API response body.
        Debug::printResponse($responseBody, $this->_parent->truncatedDebug, $path, $debug);
    }

    /**
     * Maps a server response onto a specific kind of result object.
     *
     * The result is placed directly inside `$responseObject`.
     *
     * @param Response              $responseObject An instance of a class object whose
     *                                              properties to fill with the response.
     * @param string                $rawResponse    A raw JSON response string
     *                                              from Instagram's server.
     * @param HttpResponseInterface $httpResponse   HTTP response object.
     * @param bool                  $silentFail     Silent fail flag.
     *
     * @throws InstagramException In case of invalid or failed API response.
     */
    public function mapServerResponse(
        Response $responseObject,
        $rawResponse,
        HttpResponseInterface $httpResponse,
        $silentFail)
    {
        // Attempt to decode the raw JSON to an array.
        // Important: Special JSON decoder which handles 64-bit numbers!
        $jsonArray = $this->api_body_decode($rawResponse, true);

        // If the server response is not an array, it means that JSON decoding
        // failed or some other bad thing happened. So analyze the HTTP status
        // code (if available) to see what really happened.
        if (!is_array($jsonArray)) {
            $httpStatusCode = $httpResponse !== null ? $httpResponse->getStatusCode() : null;
            switch ($httpStatusCode) {
                case 400:
                    throw new \InstagramAPI\Exception\BadRequestException('Invalid request options.');
                case 404:
                    throw new \InstagramAPI\Exception\NotFoundException('Requested resource does not exist.');
                default:
                    throw new \InstagramAPI\Exception\EmptyResponseException('No response from server. Either a connection or configuration error.');
            }
        }

        // Perform mapping of all response properties.
        try {
            // Assign the new object data. Only throws if custom _init() fails.
            // NOTE: False = assign data without automatic analysis.
            $responseObject->assignObjectData($jsonArray, false); // Throws.

            // Use API developer debugging? We'll throw if class lacks property
            // definitions, or if they can't be mapped as defined in the class
            // property map. But we'll ignore missing properties in our custom
            // UnpredictableKeys containers, since those ALWAYS lack keys. ;-)
            if ($this->_parent->apiDeveloperDebug) {
                // Perform manual analysis (so that we can intercept its analysis result).
                $analysis = $responseObject->exportClassAnalysis(); // Never throws.

                // Remove all "missing_definitions" errors for UnpredictableKeys containers.
                // NOTE: We will keep any "bad_definitions" errors for them.
                foreach ($analysis->missing_definitions as $className => $x) {
                    if (strpos($className, '\\Response\\Model\\UnpredictableKeys\\') !== false) {
                        unset($analysis->missing_definitions[$className]);
                    }
                }

                // If any problems remain after that, throw with all combined summaries.
                if ($analysis->hasProblems()) {
                    throw new LazyJsonMapperException(
                        $analysis->generateNiceSummariesAsString()
                    );
                }
            }
        } catch (LazyJsonMapperException $e) {
            // Since there was a problem, let's help our developers by
            // displaying the server's JSON data in a human-readable format,
            // which makes it easy to see the structure and necessary changes
            // and speeds up the job of updating responses and models.
            try {
                // Decode to stdClass to properly preserve empty objects `{}`,
                // otherwise they would appear as empty `[]` arrays in output.
                // NOTE: Large >32-bit numbers will be transformed into strings,
                // which helps us see which numeric values need "string" type.
                $jsonObject = $this->api_body_decode($rawResponse, false);
                if (is_object($jsonObject)) {
                    $prettyJson = @json_encode(
                        $jsonObject,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                    );
                    if ($prettyJson !== false) {
                        Debug::printResponse(
                            'Human-Readable Response:'.PHP_EOL.$prettyJson,
                            false // Not truncated.
                        );
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors.
            }

            // Exceptions will only be thrown if API developer debugging is
            // enabled and finds a problem. Either way, we should re-wrap the
            // exception to our native type instead. The message gives enough
            // details and we don't need to know the exact Lazy sub-exception.
            throw new InstagramException($e->getMessage());
        }

        // Save the HTTP response object as the "getHttpResponse()" value.
        $responseObject->setHttpResponse($httpResponse);

        // Throw an exception if the API response was unsuccessful.
        // NOTE: It will contain the full server response object too, which
        // means that the user can look at the full response details via the
        // exception itself.
        if (!$responseObject->isOk() || ($responseObject->hasStepName() && $responseObject->getStepName())) {
            if ($responseObject instanceof \InstagramAPI\Response\DirectSendItemResponse && $responseObject->getPayload() !== null) {
                // Check is response is array
                // Can be both DirectSendItemPayload and DirectSendItemPayload[].
                if (is_array($responseObject->getPayload())) {
                    // Temporary solution
                    if (isset($responseObject->getPayload()[0])) {
                        $message = $responseObject->getPayload()[0]->getMessage();
                    } else {
                        $msg = explode(":",  implode(",", $responseObject->getPayload()), 2);
                        $message = isset($msg[1]) ? $msg[1] : $msg[0];
                    }
                } else {
                    $message = $responseObject->getPayload()->getMessage();
                }
            } else {
                $message = $responseObject->getMessage();
            }

            if ($silentFail !== true) {
                try {
                    ServerMessageThrower::autoThrow(
                        get_class($responseObject),
                        $message,
                        $responseObject,
                        $httpResponse
                    );
                } catch (LoginRequiredException $e) {
                    // Instagram told us that our session is invalid (that we are
                    // not logged in). Update our cached "logged in?" state. This
                    // ensures that users with various retry-algorithms won't hammer
                    // their server. When this flag is false, ALL further attempts
                    // at AUTHENTICATED requests will be aborted by our library.
                    $this->_parent->isMaybeLoggedIn = false;

                    throw $e; // Re-throw.
                }
            }
        }
    }

    /**
     * Helper which builds in the most important Guzzle options.
     *
     * Takes care of adding all critical options that we need on every request.
     * Such as cookies and the user's proxy. But don't call this function
     * manually. It's automatically called by _guzzleRequest()!
     *
     * @param array $guzzleOptions The options specific to the current request.
     *
     * @return array A guzzle options array.
     */
    protected function _buildGuzzleOptions(
        array $guzzleOptions = [])
    {
        $criticalOptions = [
            'cookies' => ($this->_cookieJar instanceof CookieJar ? $this->_cookieJar : false),
            'verify'  => file_exists('/etc/ssl/certs/cacert.pem') ? '/etc/ssl/certs/cacert.pem' : $this->_verifySSL,
            'proxy'   => ($this->_proxy !== null ? $this->_proxy : null),
            'curl'    => [
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2, // Make http client work with HTTP 2/0
                CURLOPT_SSLVERSION => 1,
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ];

        // Critical options always overwrite identical keys in regular opts.
        // This ensures that we can't screw up the proxy/verify/cookies.
        $finalOptions = array_merge($guzzleOptions, $criticalOptions);

        // Now merge any specific Guzzle cURL-backend overrides. We must do this
        // separately since it's in an associative array and we can't just
        // overwrite that whole array in case the caller had curl options.
        if (!array_key_exists('curl', $finalOptions)) {
            $finalOptions['curl'] = [];
        }

        // Add their network interface override if they want it.
        // This option MUST be non-empty if set, otherwise it breaks cURL.
        if (is_string($this->_outputInterface) && $this->_outputInterface !== '') {
            $finalOptions['curl'][CURLOPT_INTERFACE] = $this->_outputInterface;
        }
        if ($this->_resetConnection) {
            $finalOptions['curl'][CURLOPT_FRESH_CONNECT] = true;
            $this->_resetConnection = false;
        }

        return $finalOptions;
    }

    /**
     * Wraps Guzzle's request and adds special error handling and options.
     *
     * Automatically throws exceptions on certain very serious HTTP errors. And
     * re-wraps all Guzzle errors to our own internal exceptions instead. You
     * must ALWAYS use this (or _apiRequest()) instead of the raw Guzzle Client!
     * However, you can never assume the server response contains what you
     * wanted. Be sure to validate the API reply too, since Instagram's API
     * calls themselves may fail with a JSON message explaining what went wrong.
     *
     * WARNING: This is a semi-lowlevel handler which only applies critical
     * options and HTTP connection handling! Most functions will want to call
     * _apiRequest() instead. An even higher-level handler which takes care of
     * debugging, server response checking and response decoding!
     *
     * @param HttpRequestInterface $request       HTTP request to send.
     * @param array                $guzzleOptions Extra Guzzle options for this request.
     *
     * @throws \InstagramAPI\Exception\NetworkException                For any network/socket related errors.
     * @throws \InstagramAPI\Exception\ThrottledException              When we're throttled by server.
     * @throws \InstagramAPI\Exception\RequestHeadersTooLargeException When request is too large.
     *
     * @return HttpResponseInterface
     */
    protected function _guzzleRequest(
        HttpRequestInterface $request,
        array $guzzleOptions = [])
    {
        // Add critically important options for authenticating the request.
        $guzzleOptions = $this->_buildGuzzleOptions($guzzleOptions);

        // Attempt the request. Will throw in case of socket errors!
        try {
            $response = $this->_guzzleClient->send($request, $guzzleOptions);
        } catch (\Exception $e) {
            // Re-wrap Guzzle's exception using our own NetworkException.
            throw new \InstagramAPI\Exception\NetworkException($e);
        }

        // Detect very serious HTTP status codes in the response.
        $httpCode = $response->getStatusCode();
        switch ($httpCode) {
        case 429: // "429 Too Many Requests"
            throw new \InstagramAPI\Exception\ThrottledException('Throttled by Instagram because of too many API requests.');
            break;
        case 431: // "431 Request Header Fields Too Large"
            throw new \InstagramAPI\Exception\RequestHeadersTooLargeException('The request start-line and/or headers are too large to process.');
            break;
        // WARNING: Do NOT detect 404 and other higher-level HTTP errors here,
        // since we catch those later during steps like mapServerResponse()
        // and autoThrow. This is a warning to future contributors!
        }

        // We'll periodically auto-save our cookies at certain intervals. This
        // complements the "onCloseUser" and "login()/logout()" force-saving.
        if ((time() - $this->_cookieJarLastSaved) > self::COOKIE_AUTOSAVE_INTERVAL) {
            $this->saveCookieJar();
        }

        // The response may still have serious but "valid response" errors, such
        // as "400 Bad Request". But it's up to the CALLER to handle those!
        return $response;
    }

    /**
     * Internal wrapper around _guzzleRequest().
     *
     * This takes care of many common additional tasks needed by our library,
     * so you should try to always use this instead of the raw _guzzleRequest()!
     *
     * Available library options are:
     * - 'noDebug': Can be set to TRUE to forcibly hide debugging output for
     *   this request. The user controls debugging globally, but this is an
     *   override that prevents them from seeing certain requests that you may
     *   not want to trigger debugging (such as perhaps individual steps of a
     *   file upload process). However, debugging SHOULD be allowed in MOST cases!
     *   So only use this feature if you have a very good reason.
     * - 'debugUploadedBody': Set to TRUE to make debugging display the data that
     *   was uploaded in the body of the request. DO NOT use this if your function
     *   uploaded binary data, since printing those bytes would kill the terminal!
     * - 'debugUploadedBytes': Set to TRUE to make debugging display the size of
     *   the uploaded body data. Should ALWAYS be TRUE when uploading binary data.
     *
     * @param HttpRequestInterface $request        HTTP request to send.
     * @param array                $guzzleOptions  Extra Guzzle options for this request.
     * @param array                $libraryOptions Additional options for controlling Library features
     *                                             such as the debugging output.
     *
     * @throws \InstagramAPI\Exception\NetworkException   For any network/socket related errors.
     * @throws \InstagramAPI\Exception\ThrottledException When we're throttled by server.
     *
     * @return HttpResponseInterface
     */
    protected function _apiRequest(
        HttpRequestInterface $request,
        array $guzzleOptions = [],
        array $libraryOptions = [])
    {
        // Perform the API request and retrieve the raw HTTP response body.
        $guzzleResponse = $this->_guzzleRequest($request, $guzzleOptions);

        // Debugging (must be shown before possible decoding error).
        if ($this->_parent->debug && (!isset($libraryOptions['noDebug']) || !$libraryOptions['noDebug']) || \InstagramAPI\Debug::$debugLog) {
            // Determine whether we should display the contents of the UPLOADED body.
            if (isset($libraryOptions['debugUploadedBody']) && $libraryOptions['debugUploadedBody']) {
                $uploadedBody = (string) $request->getBody();
                if (!strlen($uploadedBody)) {
                    $uploadedBody = null;
                }
            } else {
                $uploadedBody = null; // Don't display.
            }

            // Determine whether we should display the size of the UPLOADED body.
            if (isset($libraryOptions['debugUploadedBytes']) && $libraryOptions['debugUploadedBytes']) {
                // Calculate the uploaded bytes by looking at request's body size, if it exists.
                $uploadedBytes = $request->getBody()->getSize();
            } else {
                $uploadedBytes = null; // Don't display.
            }

            $this->_printDebug(
                $request->getMethod(),
                $this->_zeroRating->rewrite((string) $request->getUri()),
                $uploadedBody,
                $uploadedBytes,
                $guzzleResponse,
                (string) $guzzleResponse->getBody(),
                $this->_parent->debug);
        }

        return $guzzleResponse;
    }

    /**
     * Perform an Instagram API call.
     *
     * @param HttpRequestInterface $request       HTTP request to send.
     * @param array                $guzzleOptions Extra Guzzle options for this request.
     *
     * @throws InstagramException
     *
     * @return HttpResponseInterface
     */
    public function api(
        HttpRequestInterface $request,
        array $guzzleOptions = [])
    {
        if ($this->_parent->isWebLogin) {
            $headers = [
                'set_headers' => [
                    // Keep the API's HTTPS connection alive in Guzzle for future
                    // re-use, to greatly speed up all further queries after this.
                    // 'Connection'                 => 'Keep-Alive',
                    'Accept-Encoding'            => Constants::ACCEPT_ENCODING_GRAPH,
                    'Accept-Language'            => $this->_parent->getAcceptLanguage()
                ],
            ];
            $headers['set_headers']['Accept'] = '*/*';
            $headers['set_headers']['x-asbd-id'] = $this->_parent->settings->get('asbd_id');
            $headers['set_headers']['X-CSRFToken'] = $this->_parent->client->getToken();
            $headers['set_headers']['X-Requested-With'] = 'XMLHttpRequest';
            $headers['set_headers']['X-IG-App-ID'] = Constants::IG_WEB_APPLICATION_ID;

            if ($this->_parent->getIsAndroid()) {
                $headers['set_headers']['User-Agent'] = sprintf('Mozilla/5.0 (Linux; Android %s; Google) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36', $this->_parent->device->getAndroidRelease());
            } else {
                $headers['set_headers']['User-Agent'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS ' . Constants::IOS_VERSION . ' like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.4 Mobile/15E148 Safari/604.1';
            }
        } else {
            $headers = [
                'set_headers' => [
                    'X-Pigeon-Session-Id'    => $this->_pigeonSession,
                    'X-Pigeon-Rawclienttime' => $this->_getPigeonRawClientTime(),
                    // Keep the API's HTTPS connection alive in Guzzle for future
                    // re-use, to greatly speed up all further queries after this.
                    // 'Connection'                 => 'Keep-Alive',
                    'Accept-Encoding'            => Constants::ACCEPT_ENCODING,
                    'Accept-Language'            => $this->_parent->getAcceptLanguage()
                ],
            ];

            $userAgent = $request->getHeader('User-Agent');
            if (!empty($userAgent)) {
                $headers['set_headers']['User-Agent'] = $userAgent;
            } else {
                $headers['set_headers']['User-Agent'] = $this->_userAgent;
            }
        }

        $this->removeEmptyCookies();

        if (!$this->_parent->isWebLogin) {
            if ($this->_parent->getIsAndroid()) {
                $headers['set_headers']['X-IG-Bandwidth-Speed-KBPS'] = strval(mt_rand(2900000, 10000000)/1000);
                $headers['set_headers']['X-IG-Bandwidth-TotalBytes-B'] = strval(mt_rand(5000000, 90000000));
                $headers['set_headers']['X-IG-Bandwidth-TotalTime-MS'] = strval(mt_rand(5000, 15000));
            }

            if ($this->_parent->_needsAuth) {
                if ($this->_authorization) {
                    $headers['set_headers']['authorization'] = $this->_authorization;
                }
                if ($this->_DIRECT_REGION_HINT) { 
                    $headers['set_headers']['ig-u-ig-direct-region-hint'] = $this->_DIRECT_REGION_HINT;
                }
                if ($this->_SHBID) { 
                    $headers['set_headers']['ig-u-shbid'] = $this->_SHBID; 
                }
                if ($this->_SHBTS) { 
                    $headers['set_headers']['ig-u-shbts'] = $this->_SHBTS; 
                }
                if ($this->_DS_USER_ID) {
                    $headers['set_headers']['ig-u-ds-user-id'] = $this->_DS_USER_ID;
                }
                if ($this->_RUR) {
                    $headers['set_headers']['ig-u-rur'] = $this->_RUR;
                }
                if ($this->_DS_USER_ID) {
                    $headers['set_headers']['ig-intended-user-id'] = $this->_DS_USER_ID;
                }
            }
        }

        if ($this->_wwwClaim) {
            $headers['set_headers']['X-IG-WWW-Claim'] = $this->_wwwClaim;
        } else {
            $headers['set_headers']['X-IG-WWW-Claim'] = 0;
        }

        // Set up headers that are required for every request.
        $request = GuzzleUtils::modifyRequest($request, $headers);
        
        // Check the Content-Type header for debugging.
        $contentType = $request->getHeader('content-type');
        $isFormData = count($contentType) && reset($contentType) === Constants::CONTENT_TYPE;

        $start = microtime(true);

        // Perform the API request.
        $response = $this->_apiRequest($request, $guzzleOptions, [
            'debugUploadedBody'  => $isFormData,
            'debugUploadedBytes' => !$isFormData,
        ]);

        if ($response->hasHeader('x-ig-set-www-claim')) {
            $this->_wwwClaim = $response->getHeaderLine('x-ig-set-www-claim');
            if ($this->_wwwClaim !== $this->_parent->settings->get('x_ig_www_claim')) {
                $this->_parent->settings->set('x_ig_www_claim', $this->_wwwClaim); // This header should be stored
            }
        }

        if (!$this->_parent->isWebLogin) {
            if ($response->hasHeader('ig-set-authorization')) {
                $this->_authorization = $response->getHeaderLine('ig-set-authorization');
                if ($this->_authorization !== $this->_parent->settings->get('authorization')) {
                    $this->_parent->settings->set('authorization', $this->_authorization); // This authorization token should be stored
                }
            }
            if ($response->hasHeader('ig-set-ig-u-ig-direct-region-hint')) {
                $this->_DIRECT_REGION_HINT = $response->getHeaderLine('ig-set-ig-u-ig-direct-region-hint');
            }
            if ($response->hasHeader('ig-set-ig-u-shbid')) {
                $this->_SHBID = $response->getHeaderLine('ig-set-ig-u-shbid');
            }
            if ($response->hasHeader('ig-set-ig-u-shbts')) {
                $this->_SHBTS = $response->getHeaderLine('ig-set-ig-u-shbts');
            }
            if ($response->hasHeader('ig-set-ig-u-ds-user-id')) {
                $this->_DS_USER_ID = $response->getHeaderLine('ig-set-ig-u-ds-user-id');
            }
            if ($response->hasHeader('ig-set-ig-u-rur')) {
                $this->_RUR = $response->getHeaderLine('ig-set-ig-u-rur');
            }
        }

        return $response;
    }

    /**
     * Decode a JSON reply from Instagram's API.
     *
     * WARNING: EXTREMELY IMPORTANT! NEVER, *EVER* USE THE BASIC "json_decode"
     * ON API REPLIES! ALWAYS USE THIS METHOD INSTEAD, TO ENSURE PROPER DECODING
     * OF BIG NUMBERS! OTHERWISE YOU'LL TRUNCATE VARIOUS INSTAGRAM API FIELDS!
     *
     * @param string $json  The body (JSON string) of the API response.
     * @param bool   $assoc When FALSE, decode to object instead of associative array.
     *
     * @return object|array|null Object if assoc false, Array if assoc true,
     *                           or NULL if unable to decode JSON.
     */
    public static function api_body_decode(
        $json,
        $assoc = true)
    {
        return @json_decode($json, $assoc, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * Get the cookies middleware instance.
     *
     * @return FakeCookies
     */
    public function fakeCookies()
    {
        return $this->_fakeCookies;
    }

    /**
     * Get the zero rating rewrite middleware instance.
     *
     * @return ZeroRating
     */
    public function zeroRating()
    {
        return $this->_zeroRating;
    }

    /**
     * Start Pigeon batch requests.
     */
    public function startEmulatingBatch()
    {
        $this->_pigeonBatch = true;
        $this->_pigeonTimestamp = microtime(true);
    }

    /**
     * Stop Pigeon batch requests.
     */
    public function stopEmulatingBatch()
    {
        $this->_pigeonBatch = false;
        $this->_pigeonTimestamp = null;
    }

    /**
     * Get Pigeon Client time.
     *
     * @return string
     */
    private function _getPigeonRawClientTime()
    {
        if ($this->_pigeonBatch === true) {
            $result = $this->_pigeonTimestamp;
            $this->_pigeonTimestamp += mt_rand(0, 100) / 1000;
        } else {
            $result = microtime(true);
        }

        return sprintf('%.3F', $result);
    }

    /**
     * Sets the last processed request.
     *
     * @param Request $endpoint The last processed request
     */
    public function setLastRequest(
        $endpoint)
    {
        $this->_lastRequest = $endpoint;
    }
    
    /**
     * Gets the last processed point.
     *
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->_lastRequest;
    }
}
