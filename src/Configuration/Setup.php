<?php

namespace Laravel\Youtube\Configuration;

use Google_Client;
use Google_Service_YouTube_LiveBroadcast;
use Google_Service_YouTube_LiveBroadcastContentDetails;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_LiveBroadcastStatus;
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

    /**
     * Setup constructor.
     * @param $app
     * @param Google_Client $client
     * @throws Exception
     */
    public function __construct($app, Google_Client $client)
    {
        $this->app = $app;
        $this->config = $this->app->config;
        $this->getConfigurations();

        if(
            !$this->idClient && !$this->secretClient
        ) {
            throw new Exception('Ops: Laravel Youtube not found configurations Google "client_id" and "client_secret", please, check yout env Laravel.');
        }

        $this->setConfigurationsGoogleCall($client);
    }

    private function getConfigurations()
    {
        $this->idClient      = $this->config->get('youtube.id_client');
        $this->secretClient  = $this->config->get('youtube.secret_client');
        $this->scopes        = $this->config->get('youtube.scopes');
        $this->prefix        = 'youtube';
        $this->redirect_uri  = 'callback';
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

    public function getClientBroadcasting(String $intialDate, String $endDate, String $titleEvent, String $privacy)
    {
        return $this->setClientBroadcasting($intialDate, $endDate, $titleEvent, $privacy);
    }

    private function setClientBroadcasting(String $intialDate, String $endDate, String $titleEvent, String $privacy)
    {
        // Define the $liveBroadcast object, which will be uploaded as the request body.
        $liveBroadcast = new Google_Service_YouTube_LiveBroadcast();

        // Add 'contentDetails' object to the $liveBroadcast object.
        $liveBroadcastContentDetails = new Google_Service_YouTube_LiveBroadcastContentDetails();
        $liveBroadcastContentDetails->setEnableClosedCaptions(true);
        $liveBroadcastContentDetails->setEnableContentEncryption(true);
        $liveBroadcastContentDetails->setEnableDvr(true);
        $liveBroadcastContentDetails->setEnableEmbed(true);
        $liveBroadcastContentDetails->setRecordFromStart(true);
        $liveBroadcastContentDetails->setStartWithSlate(true);
        $liveBroadcast->setContentDetails($liveBroadcastContentDetails);

        // Add 'snippet' object to the $liveBroadcast object.
        $liveBroadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
        $liveBroadcastSnippet->setScheduledEndTime($endDate);
        $liveBroadcastSnippet->setScheduledStartTime($intialDate);
        $liveBroadcastSnippet->setTitle($titleEvent);
        $liveBroadcast->setSnippet($liveBroadcastSnippet);

        // Add 'status' object to the $liveBroadcast object.
        $liveBroadcastStatus = new Google_Service_YouTube_LiveBroadcastStatus();
        $liveBroadcastStatus->setPrivacyStatus($privacy);
        $liveBroadcast->setStatus($liveBroadcastStatus);

        return $liveBroadcast;
    }
}