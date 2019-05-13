<?php

namespace Laravel\Youtube;

use Google_Client;
use Google_Service_YouTube;

use Laravel\Youtube\Configuration\Setup;
use Laravel\Youtube\Database\Database;

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
     * DB Client
     *
     * @var Database
     */
    protected $db;

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

        $this->db = new Database();

        $this->youtube = new Google_Service_YouTube($this->client);

        $this->verifyTokenInternal();
    }

    /**
     * Verify Token User
     * @return void
     */
    private function verifyTokenInternal()
    {
        if ($accessToken = $this->db->getToken()) {
            $this->client->setAccessToken($accessToken);
        }
    }

    /**
     * Verify Video Exist
     * @param String $id
     * @return boolean
     */
    public function checkExistVideo(String $id)
    {
        $this->userToken();

        $response = $this->youtube->videos->listVideos('status', ['id' => $id]);

        return !empty($response->items);
    }

    /**
     * Save Token and use CallBack
     * @param String $token
     * @return void
     */
    public function saveTokenCallBack($token)
    {
        $this->db->saveToken($token);
    }

    /**
     * @throws \Exception
     */
    private function userToken()
    {
        
        if (is_null($accessToken = $this->client->getAccessToken())) {
            if($this->app->config->get('youtube.redirect_auth')){
                $uri = $this->client->getRedirectUri();
                header("Location: $uri");
            } else {
                throw new \Exception('An access token is required.');
            }
        }

        if($this->client->isAccessTokenExpired())
        {

            if (array_key_exists('refresh_token', $accessToken))
            {
                $this->client->refreshToken($accessToken['refresh_token']);
                $this->db->saveToken($this->client->getAccessToken());
            }
        }
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