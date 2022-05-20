<?php

return [
    'version'                         => '5.0.3',
    'debug'                           => false,
    'truncatedDebug'                  => false,
    'storageConfig'                   => [
        'storage'    => 'file',
        'basefolder' => storage_path('instagram'),
    ],

    'PATH_FFPROBE'                    => base_path('vendor/ivoglent/ffmpeg-composer-bin/bin/ffprobe', null),
    'PATH_FFMPEG'                     => base_path('vendor/ivoglent/ffmpeg-composer-bin/bin/ffmpeg', null),

    'LOGO'                            => [
        'FAVICON'  => 'public/img/favicon.ico',
        'BACKEND'  => 'public/img/logo.svg',
        'FRONTEND' => 'public/img/logo.svg',
        'MAIL'     => 'public/img/mail-logo.png',
    ],

    'SLEEP_MIN'                       => 10,
    'SLEEP_MAX'                       => 15,

    'FOLLOWER_TYPE_FOLLOWERS'         => 1,
    'FOLLOWER_TYPE_FOLLOWING'         => 2,

    'ACTION_FOLLOWERS_FOLLOW'         => 1,
    'ACTION_FOLLOWERS_UN_FOLLOW'      => 2,
    'ACTION_FOLLOWING_FOLLOW'         => 3,
    'ACTION_FOLLOWING_UN_FOLLOW'      => 4,

    'AUDIENCE_FOLLOWERS'              => 1,
    'AUDIENCE_FOLLOWING'              => 2,
    'AUDIENCE_USERS_LIST'             => 3,
    'AUDIENCE_DM_LIST'                => 4,

    'POST_STATUS_SCHEDULED'           => 1,
    'POST_STATUS_PUBLISHED'           => 2,
    'POST_STATUS_FAILED'              => 3,

    'MESSAGE_TYPE_TEXT'               => 1,
    'MESSAGE_TYPE_POST'               => 2,
    'MESSAGE_TYPE_PHOTO'              => 3,
    'MESSAGE_TYPE_DISAPPEARING_PHOTO' => 4,
    'MESSAGE_TYPE_VIDEO'              => 5,
    'MESSAGE_TYPE_DISAPPEARING_VIDEO' => 6,
    'MESSAGE_TYPE_LIKE'               => 7,
    'MESSAGE_TYPE_HASHTAG'            => 8,
    'MESSAGE_TYPE_LOCATION'           => 9,
    'MESSAGE_TYPE_PROFILE'            => 10,

    'MESSAGE_STATUS_ON_QUEUE'         => 1,
    'MESSAGE_STATUS_SUCCESS'          => 2,
    'MESSAGE_STATUS_FAILED'           => 3,

    'STATISTICS_FOLLOWERS'            => 1,
    'STATISTICS_FOLLOWING'            => 2,
    'STATISTICS_MEDIA'                => 3,

    // Settings
    'SITE_DESCRIPTION'                => 'Instagram Most Wanted Automation Tool for Direct Message & Scheduled Posts.',
    'SITE_KEYWORDS'                   => 'automation tool, web direct messenger, dm pilot, instagram direct messenger, instagram messaging tool, instagram scheduled posts',
    'SITE_SKIN'                       => 'default',
    'CURRENCY_CODE'                   => 'USD',
    'CURRENCY_SYMBOL'                 => '$',
    'TRIAL_DAYS'                      => 3,
    'TRIAL_STORAGE'                   => 10,
    'TRIAL_ACCOUNTS_COUNT'            => 1,
    'TRIAL_MESSAGES_COUNT'            => 100,
    'GOOGLE_ANALYTICS'                => '',
    'SYSTEM_PROXY'                    => false,
    'CUSTOM_PROXY'                    => true,
    'DISABLE_LANDING'                 => false,

    // Send message speed
    'MESSAGE_SPEED'                   => [
        25    => 'Slow (25 messages per day, every 56 minute)',
        50    => 'Medium (50 messages per day, every 28 minute)',
        100   => 'Fast (100 messages per day, every 14 minute)',
        200   => 'Very fast (200 messages per day, every 7 minute)',
        86400 => 'Instantly (every 7-10 seconds)',
    ],

    'ENABLED_LOCALES'                 => [
        'en',
        'ru',
        'pt',
        'tr',
        'ua',
    ],

    'PERMISSIONS'                     => [
        'send-message'     => 'Send message',
        'autopilot'        => 'Autopilot',
        'direct-messenger' => 'Direct messenger',
        'posts'            => 'Scheduled Posts and Stories',
        'media-manager'    => 'Media manager',
        'rss'              => 'RSS Autoposter',
        'bot'              => 'Chat Bot',
        'lists'            => 'Message and Users lists',
        'messages-log'     => 'Messages log',
    ],
];
