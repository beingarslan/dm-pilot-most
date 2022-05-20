<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Models\Account;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InstagramAPI\Instagram;
use InstagramAPI\Response\Model\Location as InstagramLocation;
use InstagramAPI\Signatures;

class SearchController extends Controller
{
    public function location(Request $request)
    {
        if ($request->filled('account_id')) {
            return $this->_searchByAccount($request);
        } else {
            return $this->_searchByGuest($request);
        }
    }

    public function hashtag(Request $request)
    {
        $result = [];

        if ($request->filled('q') && $request->filled('account_id')) {

            $account = Account::find($request->account_id);
            if ($account) {

                $request->q = trim($request->q, '#');

                $instagram = new Instagram(config('pilot.debug'), config('pilot.truncatedDebug'), config('pilot.storageConfig'));

                if ($account->proxy) {
                    $instagram->setProxy($account->proxy->server);
                }

                $instagram->setPlatform($account->platform);

                try {
                    $instagram->login($account->username, $account->password);
                } catch (\Exception $e) {
                    Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
                }

                try {

                    $rankToken    = Signatures::generateUUID();
                    $tab          = 'recent'; // ["top","recent","places","discover"]
                    $maxId        = null;
                    $nextMediaIds = null;

                    do {

                        $response = $instagram->hashtag->getSection($request->q, $rankToken, $tab, $nextMediaIds, $maxId);

                        if ($response->isMoreAvailable()) {

                            if ($sections = $response->getSections()) {

                                foreach ($sections as $section) {
                                    if ($section->getFeedType() == 'media') {

                                        $layout_content = $section->getLayoutContent();

                                        if ($medias = $layout_content->getMedias()) {

                                            foreach ($medias as $media) {
                                                if ($__media = $media->getMedia()) {
                                                    $user                   = $__media->getUser();
                                                    $result[$user->getPk()] = $user->getUsername();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // Simulate real human behaviour
                        //if ($maxId !== null) {
                        //    sleep(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX')));
                        //}

                        //$maxId        = $response->getNextMaxId();
                        //$nextMediaIds = $response->getNextMediaIds();

                    } while ($maxId !== null);

                } catch (\Exception $e) {
                    Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
                }

            }
        }

        return response()->json($result);
    }

    public function account(Request $request)
    {
        $result      = [];
        $maxPageSize = 4;
        $currentPage = 1;

        if ($request->filled('q') && $request->filled('account_id')) {

            $account = Account::find($request->account_id);
            if ($account) {

                $request->q = trim($request->q, '@');

                $instagram = new Instagram(config('pilot.debug'), config('pilot.truncatedDebug'), config('pilot.storageConfig'));

                if ($account->proxy) {
                    $instagram->setProxy($account->proxy->server);
                }

                $instagram->setPlatform($account->platform);

                try {
                    $instagram->login($account->username, $account->password);
                } catch (\Exception $e) {
                    Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
                }

                try {

                    $userPk = $instagram->people->getUserIdForName($request->q);

                    if ($userPk) {

                        $rankToken   = Signatures::generateUUID();
                        $maxId       = null;
                        $searchQuery = null;

                        do {

                            $response = $instagram->people->getFollowers($userPk, $rankToken, $searchQuery, $maxId);

                            if ($users = $response->getUsers()) {
                                foreach ($users as $user) {
                                    $result[$user->getPk()] = $user->getUsername();
                                }
                            }

                            if ($currentPage >= $maxPageSize) {
                                $maxId = null;
                            } else {

                                // Simulate real human behaviour
                                if ($maxId !== null) {
                                    sleep(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX')));
                                }

                                $maxId = $response->getNextMaxId();
                            }

                            $currentPage++;

                        } while ($maxId !== null);
                    }

                } catch (\Exception $e) {
                    Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
                }

            }
        }

        return response()->json($result);
    }

    private function _searchByAccount(Request $request)
    {
        $result = [];

        if ($request->filled('q') && $request->filled('account_id')) {

            $account = Account::find($request->account_id);
            if ($account) {

                $instagram = new Instagram(config('pilot.debug'), config('pilot.truncatedDebug'), config('pilot.storageConfig'));

                if ($account->proxy) {
                    $instagram->setProxy($account->proxy->server);
                }

                $instagram->setPlatform($account->platform);

                try {

                    $instagram->login($account->username, $account->password);

                    $response = $instagram->location->search(0, 0, $request->q);

                    if ($response->hasVenues()) {

                        foreach ($response->getVenues() as $venue) {

                            $result[] = [
                                'id'    => $venue->getExternalId(),
                                'name'  => $venue->getName(),
                                'model' => serialize($venue),
                            ];

                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
                }
            }
        }

        return response()->json($result);
    }

    private function _searchByGuest(Request $request)
    {
        $result = [];

        try {

            // Get random free proxy
            $freeProxy = Helper::getFreeProxy();

            $clientSettings = [
                'idn_conversion' => false,
                'verify'         => false,
                'http_errors'    => false,
                'timeout'        => 10,
                'query'          => [
                    'query'   => $request->q,
                    'context' => 'blended',
                    'hl'      => 'en',
                    'count'   => 5,
                ],
            ];

            if ($freeProxy) {
                $clientSettings = array_merge($clientSettings, [
                    'proxy' => $freeProxy,
                ]);
            }

            $client   = new GuzzleClient();
            $response = $client->request('GET', 'https://www.instagram.com/web/search/topsearch', $clientSettings);

            $responseCode     = $response->getStatusCode();
            $responseContents = $response->getBody()->getContents();
            $responseJSON     = json_decode($responseContents, true);

            if ($responseCode == 200) {

                if (isset($responseJSON['places'])) {
                    $places = $responseJSON['places'];

                    foreach ($places as $__p) {

                        $location = new InstagramLocation([
                            'name'               => $__p['place']['location']['name'],
                            'address'            => $__p['place']['location']['address'],
                            'external_id'        => $__p['place']['location']['pk'],
                            'external_id_source' => 'facebook_places',
                            'lat'                => $__p['place']['location']['lat'],
                            'lng'                => $__p['place']['location']['lng'],
                        ]);

                        $result[] = [
                            'id'    => $__p['place']['location']['pk'],
                            'name'  => $__p['place']['location']['name'],
                            'model' => serialize($location),
                        ];

                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
        }

        return response()->json($result);

    }

}
