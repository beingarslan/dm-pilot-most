<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Models\Account;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::withCount(['accounts' => function ($q) {
            $q->withoutGlobalScopes();
        }])
            ->withCount(['rss' => function ($q) {
                $q->withoutGlobalScopes();
            }])
            ->withCount(['bots' => function ($q) {
                $q->withoutGlobalScopes();
            }])
            ->with('media')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $data->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhereHas('accounts', function ($q) use ($request) {
                    $q->where('username', 'like', '%' . $request->search . '%')
                        ->withoutGlobalScopes();
                });
        }

        if ($request->filled('filter')) {

            switch ($request->filter) {
                case 'has_accounts':
                    $data->whereHas('accounts', function ($q) {
                        $q->withoutGlobalScopes();
                    });
                    break;

                case 'has_bots':
                    $data->whereHas('bots', function ($q) {
                        $q->withoutGlobalScopes();
                    });
                    break;

                case 'has_rss':
                    $data->whereHas('rss', function ($q) {
                        $q->withoutGlobalScopes();
                    });
                    break;

                case 'no_accounts':
                    $data->whereDoesntHave('accounts', function ($q) {
                        $q->withoutGlobalScopes();
                    });
                    break;

                case 'on_trial':
                    $data->whereNotNull('trial_ends_at')->where('trial_ends_at', '>=', now());
                    break;

                case 'active':
                    $data->whereNotNull('package_id')->whereNotNull('package_ends_at')->where('package_ends_at', '>=', now());
                    break;

                case 'expired':
                    $data->whereNotNull('package_id')->whereNull('package_ends_at');
                    break;

                case 'has_media':
                    $data->whereHas('media');
                    break;

            }
        }

        $data = $data->paginate(10);

        $data->map(function ($item, $key) {
            $item->used_space    = Helper::bytes_to_human($item->getMedia()->sum('size'));
            $item->storage_limit = Helper::bytes_to_human($item->package->storage_limit * 1024 * 1024);
            return $item;
        });

        return view('users.index', compact(
            'data'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $packages = Package::all();

        return view('users.create', compact(
            'packages'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users',
            'password'        => 'required|string|min:6|same:password_confirmation',
            'trial_ends_at'   => 'nullable|date',
            'package_ends_at' => 'nullable|date',
        ]);

        $request->request->add([
            'password' => Hash::make($request->password),
        ]);

        if (!$request->filled('is_admin')) {
            $request->request->add([
                'is_admin' => false,
            ]);
        } else {
            $request->request->add([
                'is_admin' => true,
            ]);
        }

        $user = User::create($request->all());

        // Pre-loaded messages list
        $templates = __('templates');

        foreach ($templates['messages'] as $group => $messages) {

            $messages_list = $user->lists()->create([
                'type' => 'messages',
                'name' => $group,
            ]);

            foreach ($messages as $message) {

                $messages_list->items()->create([
                    'text' => $message,
                ]);

            }
        }

        // Pre-loaded users list
        $users_list = $user->lists()->create([
            'type' => 'users',
            'name' => __('Most followed accounts on Instagram'),
        ]);

        foreach ($templates['users'] as $username) {

            $users_list->items()->create([
                'text' => $username,
            ]);

        }

        return redirect()->route('settings.users.index')
            ->with('success', __('Created successfully'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $packages = Package::all();

        return view('users.edit', compact(
            'user',
            'packages'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|string|min:6|same:password_confirmation',
            'trial_ends_at'   => 'nullable|date',
            'package_ends_at' => 'nullable|date',
        ]);

        if ($request->filled('password')) {
            $request->request->add([
                'password' => Hash::make($request->password),
            ]);
        } else {
            $request->request->remove('password');
        }

        if (!$request->filled('is_admin')) {
            $request->request->add([
                'is_admin' => false,
            ]);
        }

        $user->update($request->all());

        return redirect()->route('settings.users.edit', $user)
            ->with('success', __('Updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id == $user->id) {
            return redirect()->route('settings.users.index')
                ->with('error', __('You can\'t remove yourself.'));
        }

        $user->messages()->withoutGlobalScopes()->delete();
        $user->statistic()->withoutGlobalScopes()->delete();

        foreach ($user->rss()->withoutGlobalScopes()->get() as $rss) {
            $rss->items()->delete();
            $rss->delete();
        }

        foreach ($user->bots()->withoutGlobalScopes()->get() as $bot) {
            $bot->qa()->delete();
            $bot->delete();
        }

        foreach ($user->accounts()->withoutGlobalScopes()->get() as $account) {

            // Delete account related API folder
            File::deleteDirectory(storage_path('instagram' . DIRECTORY_SEPARATOR . $account->username));

            $account->followers()->delete();
            $account->autopilot()->delete();
            $account->posts()->delete();
            $account->delete();
        }

        foreach ($user->lists()->withoutGlobalScopes()->get() as $list) {
            $list->items()->delete();
            $list->delete();
        }

        $user->notifications()->delete();

        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', __('Deleted successfully'));
    }

    public function profile(Request $request)
    {
        $user                    = $request->user();
        $subscribed              = $user->subscribed();
        $on_trial                = $user->onTrial();
        $subscription_title      = null;
        $subscription_expires_in = 0;

        if ($subscribed) {
            $subscription_title      = $user->package->title;
            $subscription_expires_in = $user->package_ends_at->diffInDays();
        }

        if ($on_trial) {
            $subscription_expires_in = $user->trial_ends_at->diffInDays();
        }

        return view('auth.profile', compact(
            'user',
            'subscribed',
            'on_trial',
            'subscription_title',
            'subscription_expires_in'
        ));
    }

    public function profile_update(Request $request)
    {
        $request->validate([
            'name'     => 'required|max:255',
            'password' => 'same:password_confirmation',
        ]);

        if ($request->filled('password')) {
            $request->request->add([
                'password' => Hash::make($request->password),
            ]);
        } else {
            $request->request->remove('password');
        }

        $request->user()->update($request->all());

        return redirect()->route('profile.index')
            ->with('success', __('Updated successfully'));
    }

    public function accounts(User $user)
    {
        $accounts = Account::where('user_id', $user->id)
            ->withCount([
                'messages_on_queue' => function ($q) {
                    $q->withoutGlobalScopes();
                },
                'messages_sent'     => function ($q) {
                    $q->withoutGlobalScopes();
                },
                'messages_failed'   => function ($q) {
                    $q->withoutGlobalScopes();
                },
            ])
            ->withoutGlobalScopes()
            ->get();

        return view('users.accounts', compact(
            'user',
            'accounts'
        ));
    }

    public function login_as(User $user)
    {
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
