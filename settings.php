<?php
return [
    'settings' => [
        /**
         * Fixed Settings, never change
         */
        'determineRouteBeforeAppMiddleware' => true, // Allow middlewares determine route
        'session_name' => 'trackitAdminSessName',
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__.'/../storage/logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'allowedLocales' => [
            'es',
            // 'en',
        ],
        'locales' => [
            'es' => [
                'es_ES.UTF-8',
                'es_ES',
                'es',
                'spanish'
            ],
            'en' => [
                'en_US.UTF-8',
                'en_US',
                'en',
                'english'
            ],
        ],
        /**
         * Envirement Settings, change on server
         */
        'displayErrorDetails' => false, // set to false in production
        'debug' => false, // set to false in production
        'whoops.editor' => 'sublime', // Support click to open editor
        // Renderer Settings
        'view' => [
            'template_path' => __DIR__.'/../../current/templates/',
            'twig' => [
                'cache' => __DIR__.'/../storage/cache/twig',
                'debug' => true, // set to false in production
                'auto_reload' => true,
            ],
        ],
        'api' => [
            'guzzle' => [
                'base_uri' => 'https://api.trackitsuite.com/',
            ],
            'oauth' => [
                'clientId' => 'android',    // The client ID assigned to you by the provider
                'clientSecret' => '74756db3-8361-47d4-bb7f-e28eb319647e',   // The client password assigned to you by the provider
                'redirectUri' => 'http://example.com/your-redirect-url/',
                'urlAuthorize' => 'https://api.trackitsuite.com/oauth/token',
                'urlAccessToken' => 'https://api.trackitsuite.com/oauth/token',
                'urlResourceOwnerDetails' => 'https://api.trackitsuite.com/oauth/lockdin/resource'
            ]
        ],
        'enum' => [
            'registration_type' => [
                'Asistente',
                'Acompañante',
                'Invitado',
                'VIP',
                'Ponente',
                'Prensa',
                'Staff'
            ]
        ]
    ],
];
