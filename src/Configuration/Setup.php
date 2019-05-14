<?php

namespace Laravel\Youtube\Configuration;

use Google_Client;
use Google_Service_YouTube_LiveBroadcast;
use Google_Service_YouTube_LiveBroadcastContentDetails;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_LiveBroadcastStatus;
use Google_Service_YouTube_CdnSettings;
use Google_Service_YouTube_LiveStream;
use Google_Service_YouTube_LiveStreamSnippet;
use Carbon\Carbon;
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

    public function getClientBroadcasting(String $intialDate, String $endDate, String $titleEvent, String $privacy, $objectYouTube)
    {
        return $this->setClientBroadcasting($intialDate, $endDate, $titleEvent, $privacy, $objectYouTube);
    }

    private function adjustDate(String $dateParam)
    {
        $dataFormated = Carbon::createFromFormat('Y-m-d H:i:s', $dateParam, $this->app->config->get('youtube.timezone'));
        $dataFormated = ($dataFormated < Carbon::now($this->app->config->get('youtube.timezone'))) ? Carbon::now($this->app->config->get('youtube.timezone')) : $dataFormated;
        return $dataFormated->toIso8601String();
    }

    private function setClientBroadcasting($intialDate, $endDate, $titleEvent, $privacy, $objectYouTube)
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
        $liveBroadcastContentDetails->setEnableAutoStart(true);
        $liveBroadcast->setContentDetails($liveBroadcastContentDetails);

        // Add 'snippet' object to the $liveBroadcast object.
        $liveBroadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
        $liveBroadcastSnippet->setScheduledEndTime($this->adjustDate($endDate));
        $liveBroadcastSnippet->setScheduledStartTime($this->adjustDate($intialDate));
        $liveBroadcastSnippet->setTitle($titleEvent);
        $liveBroadcast->setSnippet($liveBroadcastSnippet);

        // Add 'status' object to the $liveBroadcast object.
        $liveBroadcastStatus = new Google_Service_YouTube_LiveBroadcastStatus();
        $liveBroadcastStatus->setPrivacyStatus($privacy);
        $liveBroadcast->setStatus($liveBroadcastStatus);
        $liveBroadcast->setKind('youtube#liveBroadcast');

        // Create Event
        $broadcastsResponse = $objectYouTube->liveBroadcasts->insert('snippet,contentDetails,status', $liveBroadcast);

        $response['broadcast_response'] = $broadcastsResponse;

        $youtube_event_id = $broadcastsResponse['id'];

        $listResponse = $objectYouTube->videos->listVideos("snippet", array('id' => $youtube_event_id));
        $video = $listResponse[0];

        $videoSnippet = $video['snippet'];
        $videoSnippet['tags'] = ['video'];
        $videoSnippet['defaultAudioLanguage'] = "pt-BR";
        $videoSnippet['defaultLanguage'] = "pt-BR";

        $video['snippet'] = $videoSnippet;

        $updateResponse = $objectYouTube->videos->update("snippet", $video);
        $response['video_response'] = $updateResponse;

        //object of livestream resource [snippet][title]
        $googleYoutubeLiveStreamSnippet = new Google_Service_YouTube_LiveStreamSnippet;
        $googleYoutubeLiveStreamSnippet->setTitle($titleEvent);

        $googleYoutubeCdnSettings = new Google_Service_YouTube_CdnSettings;
        $googleYoutubeCdnSettings->setFormat("1080p");
        $googleYoutubeCdnSettings->setIngestionType('rtmp');

        // API request [inserts liveStream resource.]
        $googleYoutubeLiveStream = new Google_Service_YouTube_LiveStream;
        $googleYoutubeLiveStream->setSnippet($googleYoutubeLiveStreamSnippet);
        $googleYoutubeLiveStream->setCdn($googleYoutubeCdnSettings);
        $googleYoutubeLiveStream->setKind('youtube#liveStream');

        //execute the insert request [return an object that contains information about new stream]
        $streamsResponse = $objectYouTube->liveStreams->insert('snippet,cdn', $googleYoutubeLiveStream, array());
        $response['stream_response'] = $streamsResponse;

        //Bind the broadcast to the live stream
        $bindBroadcastResponse = $objectYouTube->liveBroadcasts->bind($broadcastsResponse['id'], 'id,contentDetails', ['streamId' => $streamsResponse['id'],]);
        $response['bind_broadcast_response'] = $bindBroadcastResponse;

        return $response;
    }
}