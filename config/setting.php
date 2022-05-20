<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable auto save
    |--------------------------------------------------------------------------
    |
    | Auto-save every time the application shuts down
    |
     */
    'auto_save'              => false,

    /*
    |--------------------------------------------------------------------------
    | Setting driver
    |--------------------------------------------------------------------------
    |
    | Select where to store the settings.
    |
    | Supported: "database", "json"
    |
     */
    'driver'                 => 'database',

    /*
    |--------------------------------------------------------------------------
    | Database driver
    |--------------------------------------------------------------------------
    |
    | Options for database driver. Enter which connection to use, null means
    | the default connection. Set the table and column names.
    |
     */
    'database'               => [
        'connection' => null,
        'table'      => 'settings',
        'key'        => 'key',
        'value'      => 'value',
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON driver
    |--------------------------------------------------------------------------
    |
    | Options for json driver. Enter the full path to the .json file.
    |
     */
    'json'                   => [
        'path' => storage_path() . '/settings.json',
    ],

    /*
    |--------------------------------------------------------------------------
    | Override application config values
    |--------------------------------------------------------------------------
    |
    | If defined, settings package will override these config values.
    |
    | Sample:
    |   "app.locale" => "settings.locale",
    |
     */
    'override'               => [
        'pilot.SITE_DESCRIPTION'          => 'SITE_DESCRIPTION',
        'pilot.SITE_KEYWORDS'             => 'SITE_KEYWORDS',
        'pilot.SITE_SKIN'                 => 'SITE_SKIN',
        'pilot.CURRENCY_CODE'             => 'CURRENCY_CODE',
        'pilot.CURRENCY_SYMBOL'           => 'CURRENCY_SYMBOL',
        'pilot.TRIAL_DAYS'                => 'TRIAL_DAYS',
        'pilot.TRIAL_STORAGE'             => 'TRIAL_STORAGE',
        'pilot.TRIAL_ACCOUNTS_COUNT'      => 'TRIAL_ACCOUNTS_COUNT',
        'pilot.TRIAL_MESSAGES_COUNT'      => 'TRIAL_MESSAGES_COUNT',
        'pilot.GOOGLE_ANALYTICS'          => 'GOOGLE_ANALYTICS',
        'pilot.SYSTEM_PROXY'              => 'SYSTEM_PROXY',
        'pilot.CUSTOM_PROXY'              => 'CUSTOM_PROXY',
        'pilot.DISABLE_LANDING'           => 'DISABLE_LANDING',
        'pilot.LOGO.FAVICON'              => 'LOGO_FAVICON',
        'pilot.LOGO.BACKEND'              => 'LOGO_BACKEND',
        'pilot.LOGO.FRONTEND'             => 'LOGO_FRONTEND',
        'pilot.LOGO.MAIL'                 => 'LOGO_MAIL',
        'pilot.ENABLED_LOCALES'           => 'ENABLED_LOCALES',

        'mail.mailers.smtp.host'          => 'MAIL_HOST',
        'mail.mailers.smtp.port'          => 'MAIL_PORT',
        'mail.from.address'               => 'MAIL_FROM_ADDRESS',
        'mail.from.name'                  => 'MAIL_FROM_NAME',
        'mail.mailers.smtp.encryption'    => 'MAIL_ENCRYPTION',
        'mail.mailers.smtp.username'      => 'MAIL_USERNAME',
        'mail.mailers.smtp.password'      => 'MAIL_PASSWORD',

        'app.locale'                      => 'APP_LOCALE',
        'app.url'                         => 'APP_URL',
        'app.name'                        => 'APP_NAME',
        'app.timezone'                    => 'APP_TIMEZONE',

        'recaptcha.api_site_key'          => 'RECAPTCHA_SITE_KEY',
        'recaptcha.api_secret_key'        => 'RECAPTCHA_SECRET_KEY',

        'services.stripe.key'             => 'STRIPE_KEY',
        'services.stripe.secret'          => 'STRIPE_SECRET',
        'services.stripe.webhook.secret'  => 'STRIPE_WEBHOOK_SECRET',

        'services.facebook.client_id'     => 'FACEBOOK_CLIENT_ID',
        'services.facebook.client_secret' => 'FACEBOOK_CLIENT_SECRET',

        'services.paypal.client_id'       => 'PAYPAL_CLIENT_ID',
        'services.paypal.secret'          => 'PAYPAL_SECRET',
        'services.paypal.sandbox'         => 'PAYPAL_SANDBOX',

        'newsletter.apiKey'               => 'MAILCHIMP_APIKEY',
        'newsletter.lists.subscribers.id' => 'MAILCHIMP_LIST_ID',

        'services.yandex.shop_id'         => 'YANDEX_SHOP_ID',
        'services.yandex.secret_key'      => 'YANDEX_SECRET_KEY',

        'services.google.client_id'       => 'GOOGLE_CLIENT_ID',
        'services.google.client_secret'   => 'GOOGLE_CLIENT_SECRET',

        'services.instamojo.api_key'      => 'INSTAMOJO_API_KEY',
        'services.instamojo.auth_token'   => 'INSTAMOJO_AUTH_TOKEN',
        'services.instamojo.test_mode'    => 'INSTAMOJO_TEST_MODE',

        'services.tinkoff.terminal_key'   => 'TINKOFF_TERMINAL_KEY',
        'services.tinkoff.secret_key'     => 'TINKOFF_SECRET_KEY',

        'services.paystack.secret'        => 'PAYSTACK_SECRET',

    ],

    /*
    |--------------------------------------------------------------------------
    | Required Extra Columns
    |--------------------------------------------------------------------------
    |
    | The list of columns required to be set up
    |
    | Sample:
    |   "user_id",
    |   "tenant_id",
    |
     */
    'required_extra_columns' => [

    ],
];
