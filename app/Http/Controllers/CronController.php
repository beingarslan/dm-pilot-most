<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    public function queue($name = '')
    {
        Artisan::call('queue:work --queue=' . $name . ' --sleep=3 --tries=1 --stop-when-empty');

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function messages()
    {
        Artisan::call('pilot:send-messages');

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function posts()
    {
        Artisan::call('pilot:publish-posts');

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function followers()
    {
        Artisan::call('pilot:get-follower followers');

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function following()
    {
        Artisan::call('pilot:get-follower following');

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function expired()
    {
        Artisan::call('pilot:expired');

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function retry()
    {
        Artisan::call('queue:retry all');

        return response()->json([
            'status' => 'success',
        ]);
    }
}
