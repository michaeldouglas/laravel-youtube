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

    /**
     * Google YouTube
     *
     * @var \Google_Service_YouTube
     */
    protected $youtube;

    public function __construct($app, Google_Client $client)
    {
        $this->app = $app;

        $this->client = (new Setup($app, $client))->getClient();

        $this->youtube = new Google_Service_YouTube($this->client);
    }

    private function checkExistVideo(int $id)
    {
        $this->userToken();
    }

    private function userToken()
    {
        dd(get_class_methods($this->client));
        die;
        if (is_null($accessToken = $this->client->getAccessToken())) {
            throw new \Exception('An access token is required.');
        }

        if($this->client->isAccessTokenExpired())
        {
            if (array_key_exists('refresh_token', $accessToken))
            {
                $this->client->refreshToken($accessToken['refresh_token']);
            }
        }
    }

    public function teste()
    {
        $this->checkExistVideo(123);
        echo "OI";
    }

    public function AuthUser()
    {
        return $this->client->createAuthUrl();
    }

    public function AuthCallback($code)
    {
        return $this->client->authenticate($code);
    }
}