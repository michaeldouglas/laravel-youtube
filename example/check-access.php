<?php

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);

use Laravel\Youtube\Youtube;
use Dotenv\Dotenv;

try {
    file_put_contents(".env", 'ID_CLIENT_GOOGLE=TESTE');
    $dotenv = Dotenv::create(__DIR__, '.env');
    $dotenv->load();
    $config = require_once __DIR__ . '/../config/youtube.php';

    $youtube = new Youtube();


    print_r($youtube);
    print_r($config);
} catch (\Exception $e) {
    printf('<pre>%s</pre>', print_r((string)$e, 1));
}