<?php

namespace App\Http\Controllers;

use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{

    public function index(Request $request)
    {
        $skins      = Storage::disk('skins')->directories();
        $currencies = config('currencies');
        $languages  = config('languages');

        $time_zones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        return view('settings.index', compact(
            'skins',
            'currencies',
            'languages',
            'time_zones'
        ));
    }

    public function localization(Request $request)
    {
        $skins           = Storage::disk('skins')->directories();
        $currencies      = config('currencies');
        $languages       = config('languages');
        $enabled_locales = config('pilot.ENABLED_LOCALES');
        $time_zones      = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        return view('settings.localization', compact(
            'skins',
            'currencies',
            'languages',
            'time_zones',
            'enabled_locales'
        ));
    }

    public function email(Request $request)
    {
        $skins      = Storage::disk('skins')->directories();
        $currencies = config('currencies');
        $languages  = config('languages');
        $time_zones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        return view('settings.email', compact(
            'skins',
            'currencies',
            'languages',
            'time_zones'
        ));
    }

    public function integrations(Request $request)
    {
        $skins      = Storage::disk('skins')->directories();
        $currencies = config('currencies');
        $languages  = config('languages');
        $time_zones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        return view('settings.integrations', compact(
            'skins',
            'currencies',
            'languages',
            'time_zones'
        ));
    }

    public function update(Request $request, $group = '')
    {
        switch ($group) {

            case 'localization':

                $request->validate([
                    'settings'                 => 'required',
                    'settings.APP_LOCALE'      => 'required',
                    'settings.CURRENCY_CODE'   => 'required',
                    'settings.APP_TIMEZONE'    => 'required',
                    'settings.ENABLED_LOCALES' => 'required|array',
                ]);

                break;

            case 'email':

                $request->validate([
                    'settings'                   => 'required',
                    'settings.MAIL_HOST'         => 'required',
                    'settings.MAIL_PORT'         => 'required|integer',
                    'settings.MAIL_USERNAME'     => 'required',
                    'settings.MAIL_PASSWORD'     => 'required',
                    'settings.MAIL_FROM_ADDRESS' => 'required|email',
                    'settings.MAIL_FROM_NAME'    => 'required',
                ]);

                if ($request->has('action') && $request->action == 'test') {

                    try {
                        $transport = new \Swift_SmtpTransport(
                            $request->input('settings.MAIL_HOST'),
                            $request->input('settings.MAIL_PORT'),
                            $request->input('settings.MAIL_ENCRYPTION')
                        );
                        $transport->setUsername($request->input('settings.MAIL_USERNAME'));
                        $transport->setPassword($request->input('settings.MAIL_PASSWORD'));

                        $mailer = new \Swift_Mailer($transport);
                        $mailer->getTransport()->start();

                        return back()->with('success', __('SMTP settings are correct'));

                    } catch (\Swift_TransportException $e) {
                        return back()->with('error', __($e->getMessage()));
                    } catch (\Exception $e) {
                        return back()->with('error', __($e->getMessage()));
                    }
                }

                break;

            case 'integrations':

                $request->validate([
                    'settings'                     => 'required',
                    'settings.PAYPAL_SANDBOX'      => 'required|boolean',
                    'settings.INSTAMOJO_TEST_MODE' => 'required|boolean',
                ]);

                break;

            default:

                $request->validate([
                    'settings'                      => 'required',
                    'settings.APP_URL'              => 'required|url',
                    'settings.APP_NAME'             => 'required',
                    'settings.SITE_SKIN'            => 'required',
                    'settings.TRIAL_DAYS'           => 'required|integer',
                    'settings.TRIAL_STORAGE'        => 'required|integer',
                    'settings.TRIAL_ACCOUNTS_COUNT' => 'required|integer',
                    'settings.TRIAL_MESSAGES_COUNT' => 'required|integer',
                    'logo_frontend'                 => 'sometimes|required|image',
                    'logo_backend'                  => 'sometimes|required|image',
                    'logo_mail'                     => 'sometimes|required|image',
                    'logo_favicon'                  => 'sometimes|required|image|dimensions:max_width=64,max_height=64',
                ]);

                $request->merge([
                    'settings' => array_merge($request->settings, [
                        'SYSTEM_PROXY' => $request->filled('settings.SYSTEM_PROXY'),
                    ]),
                ]);

                $request->merge([
                    'settings' => array_merge($request->settings, [
                        'CUSTOM_PROXY' => $request->filled('settings.CUSTOM_PROXY'),
                    ]),
                ]);

                $request->merge([
                    'settings' => array_merge($request->settings, [
                        'DISABLE_LANDING' => $request->filled('settings.DISABLE_LANDING'),
                    ]),
                ]);

                $request->merge([
                    'settings' => array_merge($request->settings, [
                        'APP_URL' => strtolower(rtrim($request->input('settings.APP_URL'), '/')),
                    ]),
                ]);

                if ($request->hasFile('logo_favicon') && $request->file('logo_favicon')->isValid()) {
                    $logo_favicon = $request->file('logo_favicon')->store('img', 'public');
                    $request->merge([
                        'settings' => array_merge($request->settings, [
                            'LOGO_FAVICON' => 'public/storage/' . $logo_favicon,
                        ]),
                    ]);
                }

                if ($request->hasFile('logo_backend') && $request->file('logo_backend')->isValid()) {
                    $logo_backend = $request->file('logo_backend')->store('img', 'public');
                    $request->merge([
                        'settings' => array_merge($request->settings, [
                            'LOGO_BACKEND' => 'public/storage/' . $logo_backend,
                        ]),
                    ]);
                }

                if ($request->hasFile('logo_frontend') && $request->file('logo_frontend')->isValid()) {
                    $logo_frontend = $request->file('logo_frontend')->store('img', 'public');
                    $request->merge([
                        'settings' => array_merge($request->settings, [
                            'LOGO_FRONTEND' => 'public/storage/' . $logo_frontend,
                        ]),
                    ]);
                }

                if ($request->hasFile('logo_mail') && $request->file('logo_mail')->isValid()) {
                    $logo_mail = $request->file('logo_mail')->store('img', 'public');
                    $request->merge([
                        'settings' => array_merge($request->settings, [
                            'LOGO_MAIL' => 'public/storage/' . $logo_mail,
                        ]),
                    ]);
                }

                break;
        }

        $settings = collect($request->settings)->filter(function ($value, $setting) {
            if (is_null($value)) {
                setting()->forget($setting);
            }
            return !is_null($value);
        });

        setting($settings->all())->save();

        Artisan::call('config:clear');

        return back()->with('success', __('Settings saved successfully'));
    }
}
