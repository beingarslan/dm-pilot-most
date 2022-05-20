<?php

namespace App\Providers;

use App\Models\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['layouts.app'], function ($view) {

            $route = Route::currentRouteName();
            $route = str_replace('.', '-', $route);
            $view->with('bodyClass', $route);

        });

        View::composer(['partials.header'], function ($view) {

            if (Auth::check()) {

                $notifications = Auth::user()->unreadNotifications()->take(7)->get();

                $view->with('notifications', $notifications);
            }

        });

        View::composer(['partials.footer', 'auth.login', 'auth.register', 'skins.*'], function ($view) {

            $current_locale  = App::getLocale();
            $languages       = config('languages');
            $enabled_locales = config('pilot.ENABLED_LOCALES');

            $languages       = array_intersect_key($languages, array_flip($enabled_locales));
            $active_language = array_key_exists($current_locale, $languages) ? $current_locale : config('app.fallback_locale');

            $view->with('languages', $languages);
            $view->with('active_language', $languages[$active_language]);

        });

        View::composer(['skins.*'], function ($view) {

            $pages = Page::withTranslation()->active()->get();

            $view->with('pages', $pages);

        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
