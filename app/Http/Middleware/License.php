<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class License
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $exists = Storage::disk('local')->exists('pilot.license');

        if ($exists) {

            $license = Storage::disk('local')->get('pilot.license');

            try {
                $decrypted = Crypt::decrypt($license);

                if (parse_url($decrypted['license'], PHP_URL_HOST) == parse_url(config('app.url'), PHP_URL_HOST)) {
                    return $next($request);
                }
            } catch (\Exception $e) {}
        }

        return redirect()->route('settings.license.check');

    }
}
