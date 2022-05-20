<?php

namespace App\Console\Commands;

use App\Library\Helper;
use App\Library\Spintax;
use App\Models\Account;
use App\Models\Bot as AccountBot;
use App\Notifications\BotDialogueEnds;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InstagramAPI\Exception\AccountDisabledException;
use InstagramAPI\Exception\ChallengeRequiredException;
use InstagramAPI\Exception\CheckpointRequiredException;
use InstagramAPI\Exception\ConsentRequiredException;
use InstagramAPI\Exception\FeedbackRequiredException;
use InstagramAPI\Exception\IncorrectPasswordException;
use InstagramAPI\Exception\InvalidUserException;
use InstagramAPI\Exception\SentryBlockException;
use InstagramAPI\Exception\ServerMessageThrower;
use InstagramAPI\Instagram;
use InstagramAPI\Push;
use InstagramAPI\Push\Notification as PushNotification;
use InstagramAPI\Realtime;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use React\EventLoop\Factory as EventLoopFactory;

class Bot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:bot {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Direct Messenger Bot';

    protected $sleepTimeout = 500; // Sleep in microseconds between actions

    public $dialogueOrdering = []; // Chat step

    public $maxDialogueOrdering = 1; // When dialogue is ends

    public $welcomeTextSent = []; // Flag welcome message

    public $transcript = []; // Collect dialogue

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $account_id = $this->argument('account_id');

        if (is_null($account_id)) {
            return $this->error('Account ID must be specified');
        }

        // Gettings account's active Bot with QA
        $account = Account::withoutGlobalScopes()
            ->whereHas('bot', function ($q) {
                $q->withoutGlobalScopes()
                    ->active();
            })
            ->find($account_id);

        if (is_null($account)) {
            return $this->error('Account ' . $account_id . ' don\'t have any active bot');
        }

        $instagram = new Instagram(
            config('pilot.debug'),
            config('pilot.truncatedDebug'),
            config('pilot.storageConfig')
        );

        if ($account->proxy) {
            $instagram->setProxy($account->proxy->server);
        }

        $instagram->setPlatform($account->platform);

        try {

            $this->info('Try to login with account: ' . $account->username);

            $loginResponse = $instagram->login($account->username, $account->password);

            if (is_null($loginResponse)) {

                $this->info('Logged in successfully');

                // Accept all pending message requests
                $this->acceptPendingRequests($instagram);

                // Start the loop
                $loop = EventLoopFactory::create();

                // Logger
                $rtcLogger = new Logger('rtc');
                $rtcLogger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));
                $rtcLogger = null;

                $pushLogger = new Logger('push');
                $pushLogger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));
                $pushLogger = null;

                // Start RTC client
                $rtc = new Realtime($instagram, $loop, $rtcLogger);

                // Start Push listener
                $push = new Push($loop, $instagram, $pushLogger);

                // Looks like someone tries to send a message
                $push->on('direct_v2_message', function (PushNotification $push) use (
                    $loop,
                    $rtc,
                    $account,
                    $instagram
                ) {

                    if ($push->getCollapseKey() == 'direct_v2_message') {

                        $threadId         = $push->getActionParam('id');
                        $threadItemId     = $push->getActionParam('x');
                        $threadRawMessage = $push->getMessage();

                        list($senderName, $messageText) = explode(':', $threadRawMessage);

                        $senderName  = trim($senderName);
                        $messageText = trim($messageText);

                        // Dialogue ordering
                        if (!array_key_exists($threadId, $this->dialogueOrdering)) {
                            $this->dialogueOrdering[$threadId] = 1;
                        }

                        // Welcome message
                        if (!array_key_exists($threadId, $this->welcomeTextSent)) {
                            $this->welcomeTextSent[$threadId] = false;
                        }

                        // Get bot instance (Since it's a long runing process, we need to get fresh QA and status on every message)
                        $bot = AccountBot::withoutGlobalScopes()
                            ->with('qa')
                            ->where('account_id', $account->id)
                            ->active()
                            ->first();

                        if ($bot) {

                            // Determine when dialogue ends
                            $this->maxDialogueOrdering = $bot->qa->max('ordering');

                            // Simulate real human behavior
                            usleep($this->sleepTimeout);

                            // Seen message
                            $rtc->markDirectItemSeen($threadId, $threadItemId);

                            // Save transcription
                            $this->transcript[$threadId][] = [
                                'sender'  => $senderName,
                                'message' => $messageText,
                            ];

                            $this->info('Incoming: ' . $messageText);

                            if ($this->welcomeTextSent[$threadId] == false && !is_null($bot->welcome_text)) {

                                // Simulate real human behavior
                                usleep($this->sleepTimeout);

                                // Start typing reply
                                $rtc->indicateActivityInDirectThread($threadId, true);

                                // Simulate real human behavior
                                usleep($this->sleepTimeout);

                                $this->info('Sending welcome message');

                                // Prepare text
                                $replyText = $this->prepareReplyText($bot->welcome_text);

                                // Save transcription
                                $this->transcript[$threadId][] = [
                                    'sender'  => $account->username,
                                    'message' => $replyText,
                                ];

                                $rtc->sendTextToDirect($threadId, $replyText);

                                // Stop typing reply
                                $rtc->indicateActivityInDirectThread($threadId, false);

                                $this->welcomeTextSent[$threadId] = true;

                            } else {

                                // Find matching questions
                                $dialogueQA = $bot->qa->where('ordering', $this->dialogueOrdering[$threadId])->first();

                                if ($dialogueQA) {

                                    $this->info('Ordering: ' . $this->dialogueOrdering[$threadId]);

                                    $this->info('Waiting to hear: ' . join(', ', $dialogueQA->hears));

                                    // To lower case all possible matches
                                    $hears = array_map('mb_strtolower', $dialogueQA->hears);

                                    // Search for possible match
                                    $isMatchedQA = Helper::wildcardSearch($hears, mb_strtolower($messageText));

                                    if ($isMatchedQA) {

                                        $this->dialogueOrdering[$threadId]++;

                                        switch ($dialogueQA['message_type']) {

                                            case config('pilot.MESSAGE_TYPE_TEXT'):

                                                // Prepare text
                                                $replyText = $this->prepareReplyText($dialogueQA['message']['text']);

                                                // Simulate real human behavior
                                                usleep($this->sleepTimeout);

                                                // Start typing reply
                                                $rtc->indicateActivityInDirectThread($threadId, true);

                                                // Save transcription
                                                $this->transcript[$threadId][] = [
                                                    'sender'  => $account->username,
                                                    'message' => $replyText,
                                                ];

                                                $rtc->sendTextToDirect($threadId, $replyText);

                                                // Stop typing reply
                                                $rtc->indicateActivityInDirectThread($threadId, false);

                                                break;

                                            case config('pilot.MESSAGE_TYPE_LIKE'):

                                                // $rtc->sendLikeToDirect($threadId);

                                                break;

                                            case config('pilot.MESSAGE_TYPE_POST'):

                                                // $rtc->sendPostToDirect($threadId);

                                                break;

                                            case config('pilot.MESSAGE_TYPE_PROFILE'):

                                                // $rtc->sendProfileToDirect($threadId);

                                                break;

                                            case config('pilot.MESSAGE_TYPE_LOCATION'):

                                                // $rtc->sendLocationToDirect($threadId);

                                                break;

                                            case config('pilot.MESSAGE_TYPE_HASHTAG'):

                                                // $rtc->sendHashtagToDirect($threadId);

                                                break;
                                        }

                                    } else {

                                        $this->info('Wrong answer');

                                        // Wrong answer
                                        $replyText = __('Sorry, I understand only: ') . join(', ', $dialogueQA->hears);

                                        // Simulate real human behavior
                                        usleep($this->sleepTimeout);

                                        // Start typing reply
                                        $rtc->indicateActivityInDirectThread($threadId, true);

                                        // Save transcription
                                        $this->transcript[$threadId][] = [
                                            'sender'  => $account->username,
                                            'message' => $replyText,
                                        ];

                                        // No any matching QA
                                        $rtc->sendTextToDirect($threadId, $replyText);

                                        // Stop typing reply
                                        $rtc->indicateActivityInDirectThread($threadId, false);

                                    }

                                    // Dialogue ends
                                    if ($this->dialogueOrdering[$threadId] > $this->maxDialogueOrdering) {

                                        $this->info('Dialogue ends');

                                        if ($bot->email) {

                                            $bot->notify((new BotDialogueEnds($this->transcript[$threadId]))->onQueue('mail'));
                                        }
                                    }

                                } else {

                                    // Start dialogue over
                                    $this->dialogueOrdering[$threadId] = 1;

                                    // Reset transcription
                                    $this->transcript[$threadId] = [];

                                    if ($bot->unknown_text) {

                                        // Spintax rotate
                                        $replyText = $this->prepareReplyText($bot->unknown_text);

                                        // Simulate real human behavior
                                        usleep($this->sleepTimeout);

                                        // Start typing reply
                                        $rtc->indicateActivityInDirectThread($threadId, true);

                                        // No any matching QA
                                        $rtc->sendTextToDirect($threadId, $replyText);

                                        // Stop typing reply
                                        $rtc->indicateActivityInDirectThread($threadId, false);
                                    }

                                }

                            }

                        } else {

                            $rtc->stop();
                            $push->stop();
                            $loop->stop();

                        }
                    }

                });

                $rtc->on('error', function (\Exception $e) use ($rtc, $loop) {

                    $rtc->stop();
                    $loop->stop();

                    $this->error('Got fatal error from RTC: ' . $e->getMessage());

                });

                $push->on('error', function (\Exception $e) use ($push, $loop) {

                    $push->stop();
                    $loop->stop();

                    $this->error('Got fatal error from Push: ' . $e->getMessage());

                });

                $push->start();
                $rtc->start();
                $loop->run();

            } else {
                $this->error('Can\'t login account: ' . $account->username);
            }

        } catch (IncorrectPasswordException $e) {

            $this->error('The password you entered is incorrect. Please try again. ' . $e->getMessage());

        } catch (InvalidUserException $e) {

            $this->error('The username you entered doesn\'t appear to belong to an account. Please check your username and try again. ' . $e->getMessage());

        } catch (SentryBlockException $e) {

            $this->error('Your account has been banned from Instagram API for spam behaviour or otherwise abusing. ' . $e->getMessage());

        } catch (AccountDisabledException $e) {

            $this->error('Your account has been disabled for violating Instagram terms. ' . $e->getMessage());

        } catch (FeedbackRequiredException $e) {

            $this->error('Feedback required. It looks like you were misusing this feature by going too fast. ' . $e->getMessage());

        } catch (CheckpointRequiredException $e) {

            $this->error('Your account is subject to verification checkpoint. Please go to instagram.com and pass checkpoint. ' . $e->getMessage());

        } catch (ChallengeRequiredException $e) {

            $this->error('Challenge required. Please re-add your account to confirm it. ' . $e->getMessage());

        } catch (ConsentRequiredException $e) {

            $this->error('You should verify and agree terms using your mobile device. ' . $e->getMessage());

        } catch (ServerMessageThrower $e) {

            $this->error('Something went wrong: ' . $e->getMessage());

        } catch (\Exception $e) {

            $this->error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());

        }

        $this->info('Bot has been stopped');
    }

    private function prepareReplyText($text = '')
    {
        // Spintax rotate
        $spintax = new Spintax();
        $text    = $spintax->process($text);

        return $text;
    }

    private function acceptPendingRequests($instagram)
    {
        $pendingInbox = $instagram->direct->getPendingInbox();

        if ($inbox = $pendingInbox->getInbox()) {

            if ($inbox->hasThreads()) {

                $pendingThreads = $inbox->getThreads();

                $pendingThreadIds = [];
                foreach ($pendingThreads as $pendingThread) {
                    $pendingThreadIds[] = $pendingThread->getThreadId();
                }

                if (count($pendingThreadIds)) {
                    $this->info('Accepting ' . count($pendingThreadIds) . ' pending message requests');
                    $instagram->direct->approvePendingThreads($pendingThreadIds);
                } else {
                    $this->info('No pending message requests');
                }
            }
        }

    }

}
