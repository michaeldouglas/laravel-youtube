<?php

namespace Laravel\Youtube\Configuration;

use Google_Client;
use Exception;

class Setup
{

    private $idClient;
    private $secretClient;
    private $scopes;
    private $app;

    /**
     * Setup constructor.
     * @param $app
     * @param Google_Client $client
     * @throws Exception
     */
    public function __construct($app, Google_Client $client)
    {
        $this->app = $app;
        $this->getConfigurations();

        if(
            !$this->idClient && !$this->secretClient
        ) {
            throw new Exception('Opps: Laravel Youtube not found configurations Google "client_id" and "client_secret", please, check yout env Laravel.');
        }

        return $this->setConfigurationsGoogleCall($client);
    }

    private function getConfigurations()
    {
        $this->idClient     = $this->app->config->get('youtube.id_client');
        $this->secretClient = $this->app->config->get('youtube.secret_client');
        $this->scopes       = $this->app->config->get('youtube.scopes');
    }

    private function setConfigurationsGoogleCall(Google_Client $client)
    {
        $client->setClientId($this->idClient);
        $client->setClientSecret($this->secretClient);
        $client->setScopes($this->scopes);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');

        return $client;
    }
}