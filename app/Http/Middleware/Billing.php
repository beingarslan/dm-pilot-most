<?php

namespace App\Http\Middleware;

use Closure;

class Billing
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
        $user = $request->user();

        if (!$user->can('admin')) {
            if (!$user->subscribed() && !$user->onTrial()) {
                return redirect()->route('billing.index');
            }
        }

        return $next($request);
    }
}
