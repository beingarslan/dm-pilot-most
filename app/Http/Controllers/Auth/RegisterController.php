<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Newsletter;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email:rfc,filter', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        if (config('recaptcha.api_site_key') && config('recaptcha.api_secret_key')) {
            $rules['g-recaptcha-response'] = 'recaptcha';
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'trial_ends_at' => now()->addDays(config('pilot.TRIAL_DAYS')),
        ]);

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

        $user->notify((new UserRegistered())->onQueue('mail'));

        // Mailchimp subscription
        if (config('newsletter.apiKey')) {
            if (!Newsletter::isSubscribed($user->email)) {
                Newsletter::subscribe($user->email, [
                    'firstName' => $user->name,
                    'lastName'  => $user->name,
                ]);
            }
        }

        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('skins.' . config('pilot.SITE_SKIN') . '.auth.register');
    }
}
