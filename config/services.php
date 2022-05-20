<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
     */

    'mailgun'   => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses'       => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe'    => [
        'model'   => App\Models\User::class,
        'key'     => env('STRIPE_KEY'),
        'secret'  => env('STRIPE_SECRET'),
        'webhook' => [
            'secret'    => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'mandrill'  => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'facebook'  => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => '/login/facebook/callback',
    ],

    'paypal'    => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret'    => env('PAYPAL_SECRET'),
        'sandbox'   => env('PAYPAL_SANDBOX', true),
    ],

    'yandex'    => [
        'shop_id'    => env('YANDEX_SHOP_ID'),
        'secret_key' => env('YANDEX_SECRET_KEY'),
    ],

    'google'    => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => '/login/google/callback',
    ],

    'instamojo' => [
        'api_key'    => env('INSTAMOJO_API_KEY'),
        'auth_token' => env('INSTAMOJO_AUTH_TOKEN'),
        'test_mode'  => env('INSTAMOJO_TEST_MODE', true),
    ],

    'tinkoff'   => [
        'terminal_key' => env('TINKOFF_TERMINAL_KEY'),
        'secret_key'   => env('TINKOFF_SECRET_KEY'),
    ],

    'paystack'  => [
        'key'    => env('PAYSTACK_KEY'),
        'secret' => env('PAYSTACK_SECRET'),
    ],

];
