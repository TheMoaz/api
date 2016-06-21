<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id'     => '391580657713962',
        'client_secret' => 'ef16bc1317ad8446567e3bec0c3cd446',
        'redirect'      => env('SITE_URL') . '/socialize/facebook',
    ],
    'google' => [
        'client_id'     => '689881681356-9gqrgfp1p28fda947f4s031b4kb1cdut.apps.googleusercontent.com',
        'client_secret' => 'wRAM9nZtgNP_OxdhN8M1nERM',
        'redirect'      => env('SITE_URL') . '/socialize/google',  
    ],
    'e4tech' => [
        'client_url'    => 'http://e4technologies.net/sms_alerts/api/v1/sendmessage.php',
        'client_id'     => 'forimazdori',
        'client_secret' => 'forimazdori',
    ],
    'twilio' => [
        'client_url'    => 'https://demo.twilio.com/welcome/sms/reply/',
        'client_number' => '+1 650-727-5455',
        'client_id'     => 'ACcc0a8a7fcc0532cae90d2d82e8475668',
        'client_secret' => '6db74cbdbd00fd00cfd158e5890e6fcd'
    ]
];
