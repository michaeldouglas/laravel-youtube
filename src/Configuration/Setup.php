<?php

namespace Laravel\Youtube\Configuration;

use Google_Client;
use Laravel\Youtube\Configuration\Broadcast\Broadcast;
use Exception;

class Setup
{
    private $idClient;
    private $secretClient;
    private $scopes;
    private $prefix;
    private $redirect_uri;
    private $app;
    private $config;
    private $client;
    private $broadcast;

    /**
     * Setup constructor.
     * @param $app
     * @param Google_Client $client
     * @throws Exception
     */
    public function __construct($app, Google_Client $client)
    {
        $this->app = $app;
        $this->broadcast = new Broadcast($app);
        $this->config = $this->app->config;
        $this->getConfigurations();

        if (
            !$this->idClient && !$this->secretClient
        ) {
            throw new Exception('Ops: Laravel Youtube not found configurations Google "client_id" and "client_secret", please, check yout env Laravel.');
        }

        $this->setConfigurationsGoogleCall($client);
    }

    private function getConfigurations()
    {
        $this->idClient = $this->config->get('youtube.id_client');
        $this->secretClient = $this->config->get('youtube.secret_client');
        $this->scopes = $this->config->get('youtube.scopes');
        $this->prefix = 'youtube';
        $this->redirect_uri = 'callback';
    }

    public function getClient()
    {
        return $this->client;
    }

    private function setConfigurationsGoogleCall(Google_Client $client)
    {
        $client->setClientId($this->idClient);
        $client->setClientSecret($this->secretClient);
        $client->setScopes($this->scopes);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setRedirectUri(secure_url($this->prefix . '/' . $this->redirect_uri));

        $this->client = $client;
    }

    /**
     * @param String $intialDate
     * @param String $endDate
     * @param String $titleEvent
     * @param String $privacy
     * @param String $language
     * @param $objectYouTube
     * @return mixed
     * @throws Exception
     */
    public function getClientBroadcasting(String $intialDate, String $endDate, String $titleEvent, String $privacy, String $language, $objectYouTube)
    {
        try{
            return $this->broadcast->createEvent($intialDate, $endDate, $titleEvent, $privacy, $language, $objectYouTube);
        } catch (Exception $e){
            throw new Exception($e->getMessage(), 1);
        }
    }
}