<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFollower;
use App\Models\User;
use App\Notifications\FollowerLog;
use Illuminate\Console\Command;
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

class CheckActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check account activity feed';

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
        $users = User::with(['accounts' => function ($q) {
            $q->withoutGlobalScopes();
        }])->whereHas('accounts', function ($q) {
            $q->withoutGlobalScopes();
        })->get();

        if ($users->count()) {

            foreach ($users as $user) {

                if (!$user->subscribed() && !$user->onTrial() && !$user->can('admin')) {
                    continue;
                }

                foreach ($user->accounts as $account) {

                    $instagram = new Instagram(config('pilot.debug'), config('pilot.truncatedDebug'), config('pilot.storageConfig'));

                    if ($account->proxy) {
                        $instagram->setProxy($account->proxy->server);
                    }

                    $instagram->setPlatform($account->platform);

                    try {

                        $this->info('Try to login with account: ' . $account->username);

                        $loginResponse = $instagram->login($account->username, $account->password);

                        if (is_null($loginResponse)) {

                            $this->info('Logged in successfully');

                            $this->info(date('H:i:s') . ': Sleeping for a while on line: ' . __LINE__);

                            // Simulate real human behavior
                            sleep(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX')));

                            // Get activity feed
                            $activity = $instagram->people->getRecentActivityInbox();

                            // Merge stories
                            $new_stories = $activity->getNewStories();
                            $old_stories = $activity->getOldStories();
                            $stories     = array_merge($new_stories, $old_stories);

                            // Collect new followers
                            $new_followers = [];

                            if ($stories) {

                                foreach ($stories as $story) {

                                    if ($story->getType() == 3 && $story->getStoryType() == 101) {

                                        foreach ($story->getArgs()->getLinks() as $link) {

                                            if ($link->getType() == 'user') {

                                                $userPk = $link->getId();

                                                $new_followers[$userPk] = null;

                                            }

                                        }

                                        if ($inline_follow = $story->getArgs()->getInlineFollow()) {

                                            $userPk   = $inline_follow->getUserInfo()->getId();
                                            $userName = $inline_follow->getUserInfo()->getUsername();

                                            $new_followers[$userPk] = $userName;

                                        }

                                    }
                                }
                            }

                            // Proceed with the new followers
                            foreach ($new_followers as $pk => $username) {

                                // Get names on empty values
                                if (is_null($username)) {

                                    $this->info(date('H:i:s') . ': Sleeping for a while on line: ' . __LINE__);

                                    // Simulate real human behavior
                                    sleep(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX')));

                                    $username = $instagram->people->getInfoById($pk)
                                        ->getUser()
                                        ->getUsername();
                                }

                                $follow = $account->followers()
                                    ->firstOrCreate(
                                        [
                                            'pk'   => $pk,
                                            'type' => config('pilot.FOLLOWER_TYPE_FOLLOWERS'),
                                        ],
                                        [
                                            'username' => $username,
                                            'fullname' => $username,
                                        ]
                                    );

                                if ($follow->wasRecentlyCreated) {

                                    $options = [
                                        'account'    => $account->username,
                                        'account_id' => $account->id,
                                        'action'     => config('pilot.ACTION_FOLLOWERS_FOLLOW'),
                                        'pk'         => $follow->pk,
                                        'username'   => $follow->username,
                                        'fullname'   => $follow->fullname,
                                    ];

                                    ProcessFollower::dispatch($options)
                                        ->delay(now()->addSeconds(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX'))))
                                        ->onQueue('autopilot');

                                    $user->notify(new FollowerLog($options));

                                    $this->line('New follower: ' . $__user->getUsername());
                                }

                            }

                        } else {
                            $this->error('Can\'t login account: ' . $account->username);
                        }

                    } catch (IncorrectPasswordException $e) {

                        $this->error('The password you entered is incorrect. Please try again. ' . $e->getMessage());

                        continue;

                    } catch (InvalidUserException $e) {

                        $this->error('The username you entered doesn\'t appear to belong to an account. Please check your username and try again. ' . $e->getMessage());

                        continue;

                    } catch (SentryBlockException $e) {

                        $this->error('Your account has been banned from Instagram API for spam behaviour or otherwise abusing. ' . $e->getMessage());

                        continue;

                    } catch (AccountDisabledException $e) {

                        $this->error('Your account has been disabled for violating Instagram terms. ' . $e->getMessage());

                        continue;

                    } catch (FeedbackRequiredException $e) {

                        $this->error('Feedback required. It looks like you were misusing this feature by going too fast. ' . $e->getMessage());

                        continue;

                    } catch (CheckpointRequiredException $e) {

                        $this->error('Your account is subject to verification checkpoint. Please go to instagram.com and pass checkpoint. ' . $e->getMessage());

                        continue;

                    } catch (ChallengeRequiredException $e) {

                        $this->error('Challenge required. Please re-add your account to confirm it. ' . $e->getMessage());

                        continue;

                    } catch (ConsentRequiredException $e) {

                        $this->error('You should verify and agree terms using your mobile device. ' . $e->getMessage());

                        continue;

                    } catch (ServerMessageThrower $e) {

                        $this->error('Something went wrong: ' . $e->getMessage());

                        continue;

                    } catch (\Exception $e) {

                        $this->error('Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());

                        continue;
                    }

                    $this->info(date('H:i:s') . ': Sleeping for a while on line: ' . __LINE__);

                    // Simulate real human behavior
                    sleep(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX')));

                    unset($instagram);
                }
            }
        } else {
            $this->info('No users found');
        }
    }
}
