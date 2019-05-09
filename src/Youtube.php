<?php

namespace Laravel\Youtube;

use Google_Client;
use Google_Service_YouTube;

use Laravel\Youtube\Configuration\Setup;

class Youtube
{

    /**
     * Application Container
     *
     * @var Application
     */
    private $app;

    /**
     * Google Client
     *
     * @var \Google_Client
     */
    protected $client;

    public function __construct($app, Google_Client $client)
    {
        $this->app = $app;

        $this->client = new Setup($app, $client);
    }

    public function teste()
    {
        dd($this->client);
        echo "OI";
    }
}