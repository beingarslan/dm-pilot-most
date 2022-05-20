<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Spintax;
use App\Models\Account;
use App\Models\Lists;
use App\Models\Message;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DMController extends Controller
{
    public function localize($locale)
    {
        $locale = array_key_exists($locale, config('languages')) ? $locale : config('app.fallback_locale');

        App::setLocale($locale);
        session()->put('locale', $locale);

        return redirect()->back();
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->take(5)
            ->get();

        $all_accounts = $user->accounts()->get();

        $accounts = $all_accounts
            ->take(5);

        $autopilots_count = $user->autopilots()
            ->count();

        $accounts_count = $all_accounts
            ->count();

        $messages_list_count = $user->lists()
            ->ofType('messages')
            ->count();

        $users_list_count = $user->lists()
            ->ofType('users')
            ->count();

        $messages_on_queue_count = $user->messages_on_queue()
            ->count();

        $messages_sent_count = $user->messages_sent()
            ->count();

        $messages_failed_count = $user->messages_failed()
            ->count();

        $messages_total = $messages_on_queue_count + $messages_sent_count + $messages_failed_count;

        $messages = [
            'on_queue' => [
                'total'      => $messages_on_queue_count,
                'percentage' => Helper::calc_bar($messages_on_queue_count, $messages_total),
            ],
            'sent'     => [
                'total'      => $messages_sent_count,
                'percentage' => Helper::calc_bar($messages_sent_count, $messages_total),
            ],
            'failed'   => [
                'total'      => $messages_failed_count,
                'percentage' => Helper::calc_bar($messages_failed_count, $messages_total),
            ],
        ];

        return view('dashboard.dashboard', compact(
            'notifications',
            'accounts',
            'all_accounts',
            'autopilots_count',
            'accounts_count',
            'messages_list_count',
            'users_list_count',
            'messages'
        ));
    }

    public function message()
    {
        $accounts       = Account::all();
        $lists          = Lists::all();
        $users_lists    = $lists->where('type', 'users');
        $messages_lists = $lists->where('type', 'messages');
        $message_speed  = config('pilot.MESSAGE_SPEED');

        return view('message.message', compact(
            'accounts',
            'users_lists',
            'messages_lists',
            'message_speed'
        ));
    }

    public function message_send(Request $request)
    {
        $used_space    = $request->user()->getMedia()->sum('size');
        $storage_limit = $request->user()->package->storage_limit * 1024 * 1024;

        if ($used_space >= $storage_limit) {
            return redirect()->route('dm.message')
                ->with('error', __('Exceed storage limit'));
        }

        $request->validate([
            'account_id'       => 'required',
            'audience'         => 'required',
            'message_type'     => 'required',
            'speed'            => 'required',
            'messages_list_id' => 'required_if:message_type,list',
            'users_list_id'    => 'required_if:audience,' . config('pilot.AUDIENCE_USERS_LIST'),
            'text'             => 'required_if:message_type,text',
            'hashtag'          => 'required_if:message_type,hashtag',
            'hashtag_text'     => 'required_if:message_type,hashtag',
            'media_id'         => 'required_if:message_type,post',
            'post_text'        => 'required_if:message_type,post',
            'photo'            => 'required_if:message_type,photo|mimetypes:image/jpeg,image/png,image/gif',
            'video'            => 'required_if:message_type,video|mimetypes:video/x-flv,video/mp4,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv',
        ]);

        $account = Account::find($request->account_id);
        if (is_null($account)) {
            return redirect()->route('dm.message')
                ->with('error', __('Account not belongs to you!'));
        }

        $audience = collect();
        switch ($request->audience) {
            case config('pilot.AUDIENCE_FOLLOWERS'):

                $audience = $account->followers()->followers()->get();

                break;

            case config('pilot.AUDIENCE_FOLLOWING'):

                $audience = $account->followers()->following()->get();

                break;

            case config('pilot.AUDIENCE_USERS_LIST'):

                $users_list = Lists::with('items')->ofType('users')->find($request->users_list_id);

                foreach ($users_list->items as $__user) {
                    $audience->push([
                        'pk'       => null,
                        'username' => $__user->text,
                        'fullname' => null,
                    ]);
                }

                break;

            case config('pilot.AUDIENCE_DM_LIST'):

                $audience = $account->getAllThreads();

                break;

        }

        $audience_count = count($audience);

        if ($audience_count == 0) {
            return redirect()->route('dm.message')
                ->with('error', __('There is no any records for selected target audience'));
        }

        $filename = null;
        try {

            $request->user()->addAllMediaFromRequest()->each(function ($fileAdder) use (&$filename) {
                $filename = $fileAdder->toMediaCollection()->getPath();
            });

        } catch (\Exception $e) {

            return redirect()->route('dm.message')
                ->with('error', __('Something went wrong with the upload: ') . $e->getMessage());

        }

        $days        = floor($audience_count / $request->speed);
        $each_minute = floor(60 * 24 / $request->speed);
        $now         = now()->subMinutes($each_minute);

        foreach ($audience as $receiver) {

            $message = null;

            switch ($request->message_type) {

                case 'list':
                    $message_type = config('pilot.MESSAGE_TYPE_TEXT');
                    $message      = Lists::ofType('messages')->find($request->messages_list_id)->getText();
                    break;

                case 'text':
                    $message_type = config('pilot.MESSAGE_TYPE_TEXT');
                    $message      = $request->text;
                    break;

                case 'like':
                    $message_type = config('pilot.MESSAGE_TYPE_LIKE');
                    break;

                case 'hashtag':
                    $message_type = config('pilot.MESSAGE_TYPE_HASHTAG');
                    $message      = $request->hashtag_text;
                    break;

                case 'photo':
                    if ($request->has('disappearing')) {
                        $message_type = config('pilot.MESSAGE_TYPE_DISAPPEARING_PHOTO');
                    } else {
                        $message_type = config('pilot.MESSAGE_TYPE_PHOTO');
                    }
                    break;

                case 'video':
                    if ($request->has('disappearing')) {
                        $message_type = config('pilot.MESSAGE_TYPE_DISAPPEARING_VIDEO');
                    } else {
                        $message_type = config('pilot.MESSAGE_TYPE_VIDEO');
                    }
                    break;

                case 'post':
                    $message_type = config('pilot.MESSAGE_TYPE_POST');
                    $message      = $request->post_text;
                    break;

                default:
                    break;
            }

            if ($message) {
                $spintax = new Spintax();
                $message = $spintax->process($message);
            }

            if (array_key_exists('thread_id', $receiver)) {

                $to = [
                    'thread_id' => $receiver['thread_id'],
                ];

            } else {
                $to = [
                    'users' => [
                        [
                            'pk'       => $receiver['pk'],
                            'username' => $receiver['username'],
                            'fullname' => $receiver['fullname'],
                        ],
                    ],
                ];
            }

            $options = [
                'to'       => $to,
                'message'  => $message,
                'filename' => $filename,
                'hashtag'  => $request->hashtag,
                'media_id' => $request->media_id,
            ];

            Message::create([
                'user_id'      => $request->user()->id,
                'account_id'   => $account->id,
                'message_type' => $message_type,
                'options'      => $options,
                'status'       => config('pilot.MESSAGE_STATUS_ON_QUEUE'),
                'send_at'      => $now->addMinutes($each_minute),
            ]);

        }

        return redirect()->route('dm.message')
            ->with('success', __('pilot.dm_queue_sucess', [
                'minute'         => $each_minute,
                'days'           => $days,
                'audience_count' => $audience_count,
            ]));
    }

    public function notifications(Request $request)
    {
        $data = $request->user()
            ->notifications()
            ->paginate(10);

        return view('notifications.notifications', compact(
            'data'
        ));
    }

    public function mark_notifications(Request $request)
    {
        if ($request->has('delete')) {

            $request->user()
                ->notifications()
                ->delete();

            return redirect()->route('notifications')
                ->with('success', __('All notifications are cleared.'));

        } else {

            $request->user()
                ->unreadNotifications()
                ->update(['read_at' => now()]);

            return redirect()->route('notifications')
                ->with('success', __('All notifications marked as read.'));

        }
    }

    public function log(Request $request)
    {
        $accounts = Account::all();

        $data = Message::with('account')
            ->where(function ($q) use ($request) {
                if ($request->filled('status')) {
                    $q->where('status', $request->status);
                }
                if ($request->filled('account')) {
                    $q->where('account_id', $request->account);
                }
            })
            ->orderByDesc('send_at')
            ->paginate(10);

        return view('log.log', compact(
            'accounts',
            'data'
        ));
    }

    public function log_clear(Request $request)
    {
        Message::where('status', config('pilot.MESSAGE_STATUS_ON_QUEUE'))
            ->where(function ($q) use ($request) {
                if ($request->filled('account')) {
                    $q->where('account_id', $request->account);
                }
            })->delete();

        return redirect()->route('log.view')
            ->with('success', __('Log cleared successfully.'));
    }

    public function landing(Request $request)
    {
        $packages        = [];
        $currency_code   = config('pilot.CURRENCY_CODE');
        $currency_symbol = config('pilot.CURRENCY_SYMBOL');
        $skin            = config('pilot.SITE_SKIN');
        $user            = $request->user();

        try {
            $packages = Package::visible()->get();
        } catch (\Exception $e) {

        }

        return view('skins.' . $skin . '.index', compact(
            'packages',
            'currency_code',
            'currency_symbol',
            'user'
        ));
    }

    public function page(Request $request, Page $page)
    {
        $skin = config('pilot.SITE_SKIN');
        $user = $request->user();

        return view('skins.' . $skin . '.page', compact(
            'page',
            'user'
        ));
    }

}
