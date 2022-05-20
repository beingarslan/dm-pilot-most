<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFollower;
use App\Models\Statistic;
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
use InstagramAPI\Signatures;

class GetFollower extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * @var type (followers | following)
     */
    protected $signature = 'pilot:get-follower {type=followers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get followers / following';

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
        $follow_type = ($this->argument('type') == 'following'
            ? config('pilot.FOLLOWER_TYPE_FOLLOWING')
            : config('pilot.FOLLOWER_TYPE_FOLLOWERS')
        );

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

                if ($follow_type == config('pilot.FOLLOWER_TYPE_FOLLOWERS')) {
                    $sorted = $user->accounts->sortBy('followers_sync_at');
                } else {
                    $sorted = $user->accounts->sortBy('following_sync_at');
                }

                $user->accounts = $sorted->values();

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

                            $this->info(date('H:i:s') . ': Getting self info..');

                            $__user = $instagram->people->getSelfInfo()->getUser();

                            $account->posts_count     = $__user->getMediaCount();
                            $account->followers_count = $__user->getFollowerCount();
                            $account->following_count = $__user->getFollowingCount();

                            // Collect statistics
                            Statistic::withoutGlobalScopes()->updateOrCreate([
                                'user_id'    => $account->user_id,
                                'account_id' => $account->id,
                                'sync_at'    => now()->toDateString(),
                                'type'       => config('pilot.STATISTICS_FOLLOWERS'),
                            ], [
                                'count' => $account->followers_count,
                            ]);

                            Statistic::withoutGlobalScopes()->updateOrCreate([
                                'user_id'    => $account->user_id,
                                'account_id' => $account->id,
                                'sync_at'    => now()->toDateString(),
                                'type'       => config('pilot.STATISTICS_FOLLOWING'),
                            ], [
                                'count' => $account->following_count,
                            ]);

                            Statistic::withoutGlobalScopes()->updateOrCreate([
                                'user_id'    => $account->user_id,
                                'account_id' => $account->id,
                                'sync_at'    => now()->toDateString(),
                                'type'       => config('pilot.STATISTICS_MEDIA'),
                            ], [
                                'count' => $account->posts_count,
                            ]);

                            $audience     = null;
                            $rankToken    = Signatures::generateUUID();
                            $maxId        = null;
                            $page         = 1;
                            $account_list = collect();

                            do {

                                $this->info(date('H:i:s') . ': Sleeping for a while on line: ' . __LINE__);

                                // Simulate real human behavior
                                sleep(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX')));

                                $this->info('Gathering audience on page: ' . $page);

                                if ($follow_type == config('pilot.FOLLOWER_TYPE_FOLLOWERS')) {
                                    $audience = $instagram->people->getSelfFollowers($rankToken, null, $maxId);
                                } else {
                                    $audience = $instagram->people->getSelfFollowing($rankToken, null, $maxId);
                                }

                                if ($audience) {

                                    foreach ($audience->getUsers() as $__user) {

                                        $account_list->push([
                                            'pk'       => $__user->getPk(),
                                            'username' => $__user->getUsername(),
                                            'fullname' => $__user->getFullName(),
                                        ]);
                                    }
                                }

                                // Pagination
                                $maxId = $audience->getNextMaxId();
                                $page++;

                            } while ($maxId !== null);

                            /**
                             * Process gathered accounts
                             *
                             * Detect new followers / following
                             * Which are not in `followers` table
                             */
                            foreach ($account_list as $check_account) {

                                $follow = $account->followers()
                                    ->firstOrCreate(
                                        [
                                            'pk'   => $check_account['pk'],
                                            'type' => $follow_type,
                                        ],
                                        [
                                            'username' => $check_account['username'],
                                            'fullname' => $check_account['fullname'],
                                        ]
                                    );

                                /**
                                 * New follower
                                 * Do nothing on first sync
                                 */
                                if ($follow->wasRecentlyCreated) {

                                    if ($follow_type == config('pilot.FOLLOWER_TYPE_FOLLOWERS')) {
                                        $sync_at = $account->followers_sync_at;
                                    } else {
                                        $sync_at = $account->following_sync_at;
                                    }

                                    if ($sync_at) {

                                        $options = [
                                            'account'    => $account->username,
                                            'account_id' => $account->id,
                                            'action'     => ($follow_type == config('pilot.FOLLOWER_TYPE_FOLLOWING')
                                                ? config('pilot.ACTION_FOLLOWING_FOLLOW')
                                                : config('pilot.ACTION_FOLLOWERS_FOLLOW')),
                                            'pk'         => $follow->pk,
                                            'username'   => $follow->username,
                                            'fullname'   => $follow->fullname,
                                        ];

                                        ProcessFollower::dispatch($options)
                                            ->delay(now()->addSeconds(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX'))))
                                            ->onQueue('autopilot');

                                        $user->notify(new FollowerLog($options));

                                        $this->line('New follow: ' . $follow->username);
                                    }
                                }
                            }

                            /**
                             * Detect un-followers
                             * Which are in `followers` table but not in $account_list
                             */
                            $un_folowers = $account->followers()
                                ->where('type', $follow_type)
                                ->whereNotIn('pk', $account_list->pluck('pk'))
                                ->get();

                            foreach ($un_folowers as $unfollow) {

                                $options = [
                                    'account'    => $account->username,
                                    'account_id' => $account->id,
                                    'action'     => ($follow_type == config('pilot.FOLLOWER_TYPE_FOLLOWING')
                                        ? config('pilot.ACTION_FOLLOWING_UN_FOLLOW')
                                        : config('pilot.ACTION_FOLLOWERS_UN_FOLLOW')),
                                    'pk'         => $unfollow->pk,
                                    'username'   => $unfollow->username,
                                    'fullname'   => $unfollow->fullname,
                                ];

                                ProcessFollower::dispatch($options)
                                    ->delay(now()->addSeconds(rand(config('pilot.SLEEP_MIN'), config('pilot.SLEEP_MAX'))))
                                    ->onQueue('autopilot');

                                $user->notify(new FollowerLog($options));

                                $unfollow->delete();

                                $this->line('New un-follow: ' . $unfollow->username);
                            }

                            if ($follow_type == config('pilot.FOLLOWER_TYPE_FOLLOWERS')) {
                                $account->followers_sync_at = now();
                            } else {
                                $account->following_sync_at = now();
                            }

                            $account->save();

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
