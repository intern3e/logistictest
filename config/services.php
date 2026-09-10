<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'ocr' => [
        'url' => env('OCR_SERVICE_URL', 'http://192.168.1.148:8010'),
    ],
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'line' => [
        'channel_access_token' => env('LINE_CHANNEL_ACCESS_TOKEN'),
        'user_id'              => env('LINE_USER_ID'),
    ],
    'nest' => [
        'url' => env('NEST_PUBLIC_URL'),
        'public_key' => env('NEST_PUBLIC_KEY'),
    ],

    /*
    | SSO client: server_update
    | local: http://192.168.1.169:8000  |  prod: http://server_update:8000
    | เวลาขึ้น prod แก้แค่ SSO_CLIENT_UPDATE_URL ใน .env แล้วรัน:
    |   php artisan db:seed --class=SsoClientUpdateSeeder
    */
    'sso' => [
        'client_update_url'    => env('SSO_CLIENT_UPDATE_URL', 'http://192.168.1.169:8000'),
        'client_update_secret' => env('SSO_CLIENT_UPDATE_SECRET', 'server_update'),
    ],
];
