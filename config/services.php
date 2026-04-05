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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | Orbit WhatsApp API (https://orbitwaapi.site/docs)
    | POST .../messages/send dengan Authorization: Bearer <API Key>
    */
    'orbit_wa' => [
        'base_url' => rtrim(env('ORBIT_WA_BASE_URL', 'https://orbitwaapi.site/api/v1'), '/'),
        'api_key' => env('ORBIT_WA_API_KEY'),
    ],

    /*
    | TokoVoucher — dok: error HTTP/timeout dianggap pending hingga callback atau cek status berkala.
    | Host transaksi: https://api.tokovoucher.net atau http://trx-ip.tokovoucher.net (jalur IP, bila diarahkan TokoVoucher).
    | IP 188.166.243.56: biasanya untuk whitelist callback masuk ke server/firewall Anda (bukan IP outbound).
    */
    'tokovoucher' => [
        'transaction_base' => rtrim(env('TOKOVOUCHER_TRANSACTION_BASE', 'https://api.tokovoucher.net'), '/'),
        'force_ipv4' => filter_var(env('TOKOVOUCHER_FORCE_IPV4', true), FILTER_VALIDATE_BOOLEAN),
    ],

];
