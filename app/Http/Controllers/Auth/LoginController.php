<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserRegistered;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Newsletter;

class LoginController extends Controller
{
    private $providers = [
        'facebook',
        'google',
    ];

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('skins.' . config('pilot.SITE_SKIN') . '.auth.login');
    }

    /**
     * Redirect the user to the authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login');
        }

        $client = new GuzzleClient([
            'idn_conversion' => false,
            'verify'         => false,
        ]);

        return Socialite::driver($provider)->setHttpClient($client)->redirect();
    }

    /**
     * Obtain the user information.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login');
        }

        try {

            $client = new GuzzleClient([
                'idn_conversion' => false,
                'verify'         => false,
            ]);

            $social = Socialite::driver($provider)->setHttpClient($client)->user();

            $user = User::firstOrCreate(
                [
                    'email' => $social->getEmail(),
                ],
                [
                    'name'          => $social->getName(),
                    'password'      => Hash::make(Str::random(40)),
                    'trial_ends_at' => now()->addDays(config('pilot.TRIAL_DAYS')),
                ]
            );

            if ($user->wasRecentlyCreated) {

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
                    'name' => 'Most followed accounts on Instagram',
                ]);

                foreach ($templates['users'] as $username) {

                    $users_list->items()->create([
                        'text' => $username,
                    ]);

                }

                // Mailchimp subscription
                if (config('newsletter.apiKey')) {
                    if (!Newsletter::isSubscribed($user->email)) {
                        Newsletter::subscribe($user->email, [
                            'firstName' => $user->name,
                            'lastName'  => $user->name,
                        ]);
                    }
                }

                $user->notify((new UserRegistered())->onQueue('mail'));
            }

            Auth::login($user);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {

            return redirect()->route('login')->with('error', $e->getMessage());

        }
    }

}
