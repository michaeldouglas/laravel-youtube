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
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/youtubepartner',
        'https://www.googleapis.com/auth/youtubepartner-channel-audit',
        'https://www.googleapis.com/auth/youtube.force-ssl'
    ],

    /**
     * Redirect Auth
     */

    'redirect_auth' => true,

    /**
     * TimeZone Events Live
     */

    'timezone' => 'America/Sao_Paulo',

    /**
     * Language Event Live
     */
    'language' => [
        "Afrikaans" => "af",
        "Azerbaijani" => "az",
        "Indonesian" => "id",
        "Malay" => "ms",
        "Bosnian" => "bs",
        "Catalan" => "ca",
        "Czech" => "cs",
        "Danish" => "da",
        "German" => "de",
        "Estonian" => "et",
        "English (United Kingdom)" => "en-GB",
        "English" => "en",
        "Spanish (Spain)" => "es",
        "Spanish (Latin America)" => "es-419",
        "Spanish (United States)" => "es-US",
        "Basque" => "eu",
        "Filipino" => "fil",
        "French" => "fr",
        "French (Canada)" => "fr-CA",
        "Galician" => "gl",
        "Croatian" => "hr",
        "Zulu" => "zu",
        "Icelandic" => "is",
        "Italian" => "it",
        "Swahili" => "sw",
        "Latvian" => "lv",
        "Lithuanian" => "lt",
        "Hungarian" => "hu",
        "Dutch" => "nl",
        "Norwegian" => "no",
        "Uzbek" => "uz",
        "Polish" => "pl",
        "Portuguese (Portugal)" => "pt-PT",
        "Portuguese (Brazil)" => "pt",
        "Romanian" => "ro",
        "Albanian" => "sq",
        "Slovak" => "sk",
        "Slovenian" => "sl",
        "Serbian (Latin)" => "sr-Latn",
        "Finnish" => "fi",
        "Swedish" => "sv",
        "Vietnamese" => "vi",
        "Turkish" => "tr",
        "Belarusian" => "be",
        "Bulgarian" => "bg",
        "Kyrgyz" => "ky",
        "Kazakh" => "kk",
        "Macedonian" => "mk",
        "Mongolian" => "mn",
        "Russian" => "ru",
        "Serbian" => "sr",
        "Ukrainian" => "uk",
        "Greek" => "el",
        "Armenian" => "hy",
        "Hebrew" => "iw",
        "Urdu" => "ur",
        "Arabic" => "ar",
        "Persian" => "fa",
        "Nepali" => "ne",
        "Marathi" => "mr",
        "Hindi" => "hi",
        "Bangla" => "bn",
        "Punjabi" => "pa",
        "Gujarati" => "gu",
        "Tamil" => "ta",
        "Telugu" => "te",
        "Kannada" => "kn",
        "Malayalam" => "ml",
        "Sinhala" => "si",
        "Thai" => "th",
        "Lao" => "lo",
        "Myanmar (Burmese)" => "my",
        "Georgian" => "ka",
        "Amharic" => "am",
        "Khmer" => "km",
        "Chinese" => "zh-CN",
        "Chinese (Taiwan)" => "zh-TW",
        "Chinese (Hong Kong)" => "zh-HK",
        "Japanese" => "ja",
        "Korean" => "ko"
    ],

    /**
     * Routes
     */

    'routes' => [
        'enabled' => false,
        'prefix' => 'youtube',
        'redirect_uri' => 'callback',
        'authentication_uri' => 'auth',
        'redirect_back_uri' => '/',
    ]
];