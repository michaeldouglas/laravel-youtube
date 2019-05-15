<?php

namespace Laravel\Youtube\Configuration\Broadcast;

use Google_Service_YouTube_LiveBroadcast;
use Google_Service_YouTube_LiveBroadcastContentDetails;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_LiveBroadcastStatus;
use Google_Service_YouTube_CdnSettings;
use Google_Service_YouTube_LiveStream;
use Google_Service_YouTube_LiveStreamSnippet;
use Carbon\Carbon;

class Broadcast
{
    private $liveBroadcast;
    private $liveBroadcastContentDetails;
    private $liveBroadcastSnippet;
    private $liveBroadcastStatus;
    private $broadcastsResponse;
    private $youtubeEventId;
    private $intialDate;
    private $privacy;
    private $titleEvent;
    private $endDate;
    private $response;
    private $youtube;
    private $video;

    public function __construct($app)
    {
        $this->app                         = $app;
        $this->liveBroadcast               = new Google_Service_YouTube_LiveBroadcast();
        $this->liveBroadcastContentDetails = new Google_Service_YouTube_LiveBroadcastContentDetails();
        $this->liveBroadcastSnippet        = new Google_Service_YouTube_LiveBroadcastSnippet();
        $this->liveBroadcastStatus         = new Google_Service_YouTube_LiveBroadcastStatus();
    }

    private function adjustDate(String $dateParam)
    {
        $dataFormated = Carbon::createFromFormat('Y-m-d H:i:s', $dateParam, $this->app->config->get('youtube.timezone'));
        $dataFormated = ($dataFormated < Carbon::now($this->app->config->get('youtube.timezone'))) ? Carbon::now($this->app->config->get('youtube.timezone')) : $dataFormated;
        return $dataFormated->toIso8601String();
    }

    private function setLiveBroadcast()
    {
        $this->liveBroadcastContentDetails->setEnableClosedCaptions(true);
        $this->liveBroadcastContentDetails->setEnableContentEncryption(true);
        $this->liveBroadcastContentDetails->setEnableDvr(true);
        $this->liveBroadcastContentDetails->setEnableEmbed(true);
        $this->liveBroadcastContentDetails->setRecordFromStart(true);
        $this->liveBroadcastContentDetails->setStartWithSlate(true);
        $this->liveBroadcastContentDetails->setEnableAutoStart(true);
        $this->liveBroadcast->setContentDetails($this->liveBroadcastContentDetails);

        return $this;
    }

    private function setSnippet($intialDate, $endDate, $titleEvent)
    {
        if(!is_null($endDate) && $endDate != "" && !empty($endDate)){
            $this->liveBroadcastSnippet->setScheduledEndTime($this->adjustDate($endDate));
        }
        $this->liveBroadcastSnippet->setScheduledStartTime($this->adjustDate($intialDate));
        $this->liveBroadcastSnippet->setTitle($titleEvent);
        $this->liveBroadcast->setSnippet($this->liveBroadcastSnippet);

        return $this;
    }

    /**
     * Add 'status' object to the $liveBroadcast object.
     * @param $privacy
     * @return $this
     */
    private function setLiveStatus($privacy)
    {
        $this->liveBroadcastStatus->setPrivacyStatus($privacy);
        $this->liveBroadcast->setStatus($this->liveBroadcastStatus);
        $this->liveBroadcast->setKind('youtube#liveBroadcast');

        return $this;
    }

    private function listVideo($youtube_event_id)
    {
        $listResponse = $this->youtube->videos->listVideos("snippet", array('id' => $youtube_event_id));
        $this->video = $listResponse[0];
    }

    private function creteEventBroadcast()
    {
        $this->broadcastsResponse = $this->youtube->liveBroadcasts->insert('snippet,contentDetails,status', $this->liveBroadcast);

        $this->response['broadcast_response'] = $this->broadcastsResponse;

        $this->youtubeEventId = $this->broadcastsResponse['id'];

        return $this;
    }

    private function setDefaultConfigurations($intialDate, $endDate, $titleEvent, $privacy, $objectYouTube)
    {
        $this->youtube    = $objectYouTube;
        $this->intialDate = $intialDate;
        $this->endDate    = $endDate;
        $this->titleEvent = $titleEvent;
        $this->privacy    = $privacy;
    }

    public function createEvent($intialDate, $endDate, $titleEvent, $privacy, $objectYouTube)
    {
        $this->setDefaultConfigurations($intialDate, $endDate, $titleEvent, $privacy, $objectYouTube);

        $this->setLiveBroadcast()->setSnippet($intialDate, $endDate, $titleEvent)->setLiveStatus($privacy)->creteEventBroadcast();

        $this->listVideo($this->youtubeEventId);

        $videoSnippet = $this->video['snippet'];
        $videoSnippet['tags'] = ['video'];
        $videoSnippet['defaultAudioLanguage'] = "pt-BR";
        $videoSnippet['defaultLanguage'] = "pt-BR";

        $this->video['snippet'] = $videoSnippet;

        $updateResponse = $this->youtube->videos->update("snippet", $this->video);
        $this->response['video_response'] = $updateResponse;

        //object of livestream resource title
        $googleYoutubeLiveStreamSnippet = new Google_Service_YouTube_LiveStreamSnippet;
        $googleYoutubeLiveStreamSnippet->setTitle($titleEvent);

        $googleYoutubeCdnSettings = new Google_Service_YouTube_CdnSettings;
        $googleYoutubeCdnSettings->setFormat("1080p");
        $googleYoutubeCdnSettings->setIngestionType('rtmp');

        // API request inserts liveStream resource.
        $googleYoutubeLiveStream = new Google_Service_YouTube_LiveStream;
        $googleYoutubeLiveStream->setSnippet($googleYoutubeLiveStreamSnippet);
        $googleYoutubeLiveStream->setCdn($googleYoutubeCdnSettings);
        $googleYoutubeLiveStream->setKind('youtube#liveStream');

        //execute the insert request [return an object that contains information about new stream]
        $streamsResponse = $this->youtube->liveStreams->insert('snippet,cdn', $googleYoutubeLiveStream, array());
        $this->response['stream_response'] = $streamsResponse;

        //Bind the broadcast to the live stream
        $bindBroadcastResponse = $this->youtube->liveBroadcasts->bind($this->broadcastsResponse['id'], 'id,contentDetails', ['streamId' => $streamsResponse['id'],]);
        $this->response['bind_broadcast_response'] = $bindBroadcastResponse;

        return $this->response;
    }
}