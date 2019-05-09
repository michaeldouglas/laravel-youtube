<?php

return [
    /**
     * Client ID.
     */

    'id_client' => env('ID_CLIENT_GOOGLE', null),

    /**
     * Client Secret.
     */
    'secret_client' => env('SECRET_CLIENT_GOOGLE', null),

    /**
     * Scopes.
     */
    'scopes' => [
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.upload',
        'https://www.googleapis.com/auth/youtube.readonly'
    ],
];