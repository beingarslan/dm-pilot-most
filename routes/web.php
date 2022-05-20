<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Authorization
Auth::routes();

// Localization
Route::get('lang/{locale}', 'DMController@localize')->name('localize');

// Support GET logout
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

// Pages
Route::get('page/{page:slug}', 'DMController@page')->name('page');

// Landing
if (config('pilot.DISABLE_LANDING')) {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('landing');
} else {
    Route::get('/', 'DMController@landing')->name('landing');
}

// Authorized users
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('dashboard', 'DMController@dashboard')->name('dashboard');
    Route::get('account/{account}/chart', 'AccountController@chart')->name('account.chart');
    Route::get('account/{account}/oembed', 'AccountController@oembed')->name('account.oembed');
    Route::get('account/{username}/info', 'AccountController@info')->name('account.info');

    // Profile
    Route::get('profile', 'UsersController@profile')->name('profile.index');
    Route::put('profile', 'UsersController@profile_update')->name('profile.update');

    // Only users on subscripion or on trial
    Route::middleware('billing')->group(function () {

        // Confirm Account
        Route::prefix('account/{account}')->name('account.')->group(function () {

            Route::get('confirm', 'AccountController@confirm')->name('confirm');

            Route::get('2fa', 'AccountController@two_factor')->name('2fa');
            Route::post('2fa', 'AccountController@two_factor_confirm')->name('2fa.confirm');

            Route::get('challenge/choice', 'AccountController@challenge_choice')->name('challenge.choice');
            Route::post('challenge/choice', 'AccountController@challenge_choice_confirm')->name('challenge.choice.confirm');

            Route::get('challenge/confirm', 'AccountController@challenge')->name('challenge');
            Route::post('challenge/confirm', 'AccountController@challenge_confirm')->name('challenge.confirm');

            // Export
            Route::get('export/{type}', 'AccountController@export')->name('export')->where([
                'type' => '(followers|following)',
            ]);

        });

        // Account management
        Route::resource('account', 'AccountController')->except('show');

        // Lists management
        Route::group([
            'middleware' => 'can:lists',
            'prefix'     => '{type}',
            'where'      => [
                'type' => '(users|messages)',
            ],
        ], function () {
            Route::post('multiple', 'ListController@multiple')->name('list.multiple');
            Route::resource('list', 'ListController')->except('show');
        });

        // Send message
        Route::middleware(['can:send-message'])->group(function () {
            Route::get('message', 'DMController@message')->name('dm.message');
            Route::post('message', 'DMController@message_send')->name('dm.message_send');
        });

        // Autopilot
        Route::resource('autopilot', 'AutopilotController')->except('show')->middleware('can:autopilot');

        // Notifications
        Route::get('notifications', 'DMController@notifications')->name('notifications');
        Route::put('notifications', 'DMController@mark_notifications')->name('mark.notifications');

        // Direct Messenger
        Route::middleware(['can:direct-messenger'])->group(function () {
            Route::get('direct', 'DirectController@index')->name('direct.index');
            Route::post('direct/{account}', 'DirectController@inbox')->name('direct.inbox');
            Route::post('direct/{account}/{thread_id}', 'DirectController@thread')->name('direct.thread');
            Route::post('direct/{account}/{thread_id}/send', 'DirectController@send')->name('direct.send');
        });

        // Messages log
        Route::middleware(['can:messages-log'])->group(function () {
            Route::get('log', 'DMController@log')->name('log.view');
            Route::post('log', 'DMController@log_clear')->name('log.clear');
        });

        // Post
        Route::resource('post', 'PostController')->except('show')->middleware('can:posts');

        // Media manager
        Route::get('media', 'MediaController@index')->name('media.index')->middleware('can:media-manager');
        Route::post('media', 'MediaController@upload')->name('media.upload');
        Route::get('media/files', 'MediaController@files')->name('media.files');
        Route::delete('media', 'MediaController@delete')->name('media.delete');
        Route::delete('media/clear', 'MediaController@clear')->name('media.clear');

        // Search
        Route::post('search/location', 'SearchController@location')->name('search.location');
        Route::post('search/hashtag', 'SearchController@hashtag')->name('search.hashtag');
        Route::post('search/account', 'SearchController@account')->name('search.account');

        // RSS Feeds
        Route::middleware(['can:rss'])->group(function () {
            Route::get('rss', 'RssController@setup')->name('rss.setup');
            Route::get('rss/{account}/create', 'RssController@create')->name('rss.create');
            Route::get('rss/{account}', 'RssController@index')->name('rss.index');
            Route::post('rss/{account}', 'RssController@store')->name('rss.store');
            Route::get('rss/{account}/{rss}', 'RssController@edit')->name('rss.edit');
            Route::put('rss/{account}/{rss}', 'RssController@update')->name('rss.update');
            Route::delete('rss/{account}/{rss}', 'RssController@destroy')->name('rss.destroy');
        });

        // Bot
        Route::middleware(['can:bot'])->group(function () {
            Route::get('bot', 'BotController@setup')->name('bot.setup');
            Route::get('bot/{account}', 'BotController@index')->name('bot.index');
            Route::put('bot/{account}', 'BotController@update')->name('bot.update');
        });

    });

    // Billing
    Route::get('billing', 'BillingController@index')->name('billing.index');
    Route::delete('billing', 'BillingController@cancel')->name('billing.cancel');
    Route::get('billing/{package}', 'BillingController@package')->name('billing.package');

    // Payment gateway
    Route::any('billing/{gateway}/notify', 'BillingController@gateway_notify')->name('gateway.notify')->withoutMiddleware('auth');
    Route::post('billing/{package}/{gateway}', 'BillingController@gateway_purchase')->name('gateway.purchase');
    Route::get('billing/{payment}/return', 'BillingController@gateway_return')->name('gateway.return');
    Route::get('billing/{payment}/cancel', 'BillingController@gateway_cancel')->name('gateway.cancel');

    // Administrator
    Route::middleware('can:admin')->prefix('settings')->name('settings.')->group(function () {

        Route::get('license', 'LicenseController@check')->name('license.check');
        Route::post('license', 'LicenseController@verify')->name('license.verify');

        Route::middleware('license')->group(function () {

            // Settings
            Route::get('/', 'SettingsController@index')->name('index');
            Route::get('localization', 'SettingsController@localization')->name('localization');
            Route::get('email', 'SettingsController@email')->name('email');
            Route::get('integrations', 'SettingsController@integrations')->name('integrations');

            // Packages
            Route::resource('packages', 'PackagesController')->except('show');

            // Users
            Route::resource('users', 'UsersController')->except('show');
            Route::get('users/{user}/accounts', 'UsersController@accounts')->name('users.accounts');
            Route::get('users/{user}/login', 'UsersController@login_as')->name('users.login_as');

            // Proxy
            Route::resource('proxy', 'ProxyController')->except('show');

            // Payments
            Route::get('payments', 'BillingController@payments')->name('payments');

            // Pages
            Route::resource('pages', 'PageController')->except('show');

            // Upgrade
            Route::get('upgrade', 'UpgradeController@index')->name('upgrade.index');
            Route::post('upgrade', 'UpgradeController@check')->name('upgrade.check');
            Route::put('upgrade', 'UpgradeController@upgrade')->name('upgrade.upgrade');

            // Save settings
            Route::put('{group?}', 'SettingsController@update')->name('update');

        });

    });

});

// Install
Route::middleware('installable')->group(function () {
    Route::get('install', 'InstallController@install_check')->name('install.check');
    Route::post('install', 'InstallController@install_db')->name('install.db');
    Route::get('install/setup', 'InstallController@setup')->name('install.setup');
    Route::get('install/administrator', 'InstallController@install_administrator')->name('install.administrator');
    Route::post('install/administrator', 'InstallController@install_finish')->name('install.finish');
});

// Update
Route::get('update', 'InstallController@update_check')->name('update.check');
Route::post('update', 'InstallController@update_finish')->name('update.finish');

// External Cron
Route::group([
    'prefix'     => 'cron',
    'middleware' => 'throttle:10,1',
], function () {
    Route::get('queue/{name}', 'CronController@queue')->name('cron.queue')->where([
        'name' => '(autopilot|mail)',
    ]);
    Route::get('messages', 'CronController@messages')->name('cron.messages');
    Route::get('posts', 'CronController@posts')->name('cron.posts');
    Route::get('followers', 'CronController@followers')->name('cron.followers');
    Route::get('following', 'CronController@following')->name('cron.following');
    Route::get('expired', 'CronController@expired')->name('cron.expired');
    Route::get('retry', 'CronController@retry')->name('cron.retry');
});

// Login with social accounts
Route::get('login/{provider}', '\App\Http\Controllers\Auth\LoginController@redirectToProvider')->name('login.social');
Route::get('login/{provider}/callback', '\App\Http\Controllers\Auth\LoginController@handleProviderCallback')->name('login.callback');
