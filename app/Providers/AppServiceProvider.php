<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use InstagramAPI\Instagram;
use InstagramAPI\Utils;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

        Utils::$ffprobeBin = config('pilot.PATH_FFPROBE');
        Utils::$ffmpegBin  = config('pilot.PATH_FFMPEG');

        Schema::defaultStringLength(191);

        date_default_timezone_set(config('app.timezone'));

        if (!defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'rb'));
        }

        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'wb'));
        }

        if (!defined('STDERR')) {
            define('STDERR', fopen('php://stderr', 'wb'));
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
