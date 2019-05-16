<?php

namespace Laravel\Youtube\Configuration\Broadcast;

use Google_Service_YouTube_LiveBroadcast;
use Google_Service_YouTube_LiveBroadcastContentDetails;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_LiveBroadcastStatus;
use Google_Service_YouTube_CdnSettings;
use Google_Service_YouTube_LiveStream;
use Google_Service_YouTube_LiveStreamSnippet;
use Google_Service_Exception;
use Google_Exception;
use Exception;
use Carbon\Carbon;

class Broadcast
{
    private $liveBroadcast;
    private $liveBroadcastContentDetails;
    private $liveBroadcastSnippet;
    private $liveBroadcastStatus;
    private $googleYoutubeLiveStreamSnippet;
    private $googleYoutubeCdnSettings;
    private $googleYoutubeLiveStream;
    private $broadcastsResponse;
    private $youtubeEventId;
    private $intialDate;
    private $privacy;
    private $titleEvent;
    private $endDate;
    private $response;
    private $language;
    private $youtube;
    private $video;
    private $streamsResponse;

    public function __construct($app)
    {
        $this->app                            = $app;
        $this->liveBroadcast                  = new Google_Service_YouTube_LiveBroadcast();
        $this->liveBroadcastContentDetails    = new Google_Service_YouTube_LiveBroadcastContentDetails();
        $this->liveBroadcastSnippet           = new Google_Service_YouTube_LiveBroadcastSnippet();
        $this->liveBroadcastStatus            = new Google_Service_YouTube_LiveBroadcastStatus();
        $this->googleYoutubeLiveStreamSnippet = new Google_Service_YouTube_LiveStreamSnippet;
        $this->googleYoutubeCdnSettings       = new Google_Service_YouTube_CdnSettings;
        $this->googleYoutubeLiveStream        = new Google_Service_YouTube_LiveStream;
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

    private function setSnippet()
    {
        if(!is_null($this->endDate) && $this->endDate != "" && !empty($this->endDate)){
            $this->liveBroadcastSnippet->setScheduledEndTime($this->adjustDate($this->endDate));
        }
        $this->liveBroadcastSnippet->setScheduledStartTime($this->adjustDate($this->intialDate));
        $this->liveBroadcastSnippet->setTitle($this->titleEvent);
        $this->liveBroadcast->setSnippet($this->liveBroadcastSnippet);

        return $this;
    }

    /**
     * Add 'status' object to the $liveBroadcast object.
     * @param $privacy
     * @return $this
     */
    private function setLiveStatus()
    {
        $this->liveBroadcastStatus->setPrivacyStatus($this->privacy);
        $this->liveBroadcast->setStatus($this->liveBroadcastStatus);
        $this->liveBroadcast->setKind('youtube#liveBroadcast');

        return $this;
    }

    private function listVideo()
    {
        $listResponse = $this->youtube->videos->listVideos("snippet", array('id' => $this->youtubeEventId));
        $this->video = $listResponse[0];

        return $this;
    }

    private function creteEventBroadcast()
    {
        $this->broadcastsResponse = $this->youtube->liveBroadcasts->insert('snippet,contentDetails,status', $this->liveBroadcast);

        $this->response['broadcast_response'] = $this->broadcastsResponse;

        $this->youtubeEventId = $this->broadcastsResponse['id'];

        return $this;
    }

    private function setDefaultConfigurations($intialDate, $endDate, $titleEvent, $privacy, $language, $objectYouTube)
    {
        $this->youtube    = $objectYouTube;
        $this->intialDate = $intialDate;
        $this->endDate    = $endDate;
        $this->titleEvent = $titleEvent;
        $this->privacy    = $privacy;
        $this->language   = $language;
    }

    private function setSnippetVideo()
    {
        $videoSnippet = $this->video['snippet'];
        $videoSnippet['tags'] = ['video'];
        $videoSnippet['defaultAudioLanguage'] = $this->app->config->get('youtube.language')[$this->language];
        $videoSnippet['defaultLanguage'] = $this->app->config->get('youtube.language')[$this->language];

        $this->video['snippet'] = $videoSnippet;

        $updateResponse = $this->youtube->videos->update("snippet", $this->video);

        $this->googleYoutubeLiveStreamSnippet->setTitle($this->titleEvent);

        $this->response['video_response'] = $updateResponse;
    }

    private function configurationLiveCDN()
    {
        $this->googleYoutubeCdnSettings->setFormat("1080p");
        $this->googleYoutubeCdnSettings->setIngestionType('rtmp');

        return $this;
    }

    /**
     * API request inserts liveStream resource.
     * @return mixed
     */
    private function insertLiveStream()
    {
        $this->googleYoutubeLiveStream->setSnippet($this->googleYoutubeLiveStreamSnippet);
        $this->googleYoutubeLiveStream->setCdn($this->googleYoutubeCdnSettings);
        $this->googleYoutubeLiveStream->setKind('youtube#liveStream');

        $this->streamsResponse = $this->youtube->liveStreams->insert('snippet,cdn', $this->googleYoutubeLiveStream, array());
        $this->response['stream_response'] = $this->streamsResponse;
    }

    /**
     * Bind the broadcast to the live stream
     */
    private function bindEvent()
    {
        $bindBroadcastResponse = $this->youtube->liveBroadcasts->bind($this->broadcastsResponse['id'], 'id,contentDetails', ['streamId' => $this->streamsResponse['id'],]);
        $this->response['bind_broadcast_response'] = $bindBroadcastResponse;
    }

    /**
     * Create Event Live YouTube
     * @param $intialDate
     * @param $endDate
     * @param $titleEvent
     * @param $privacy
     * @param $objectYouTube
     * @return mixed
     * @throws Google_Service_Exception
     * @throws Google_Exception
     * @throws Exception
     */
    public function createEvent($intialDate, $endDate, $titleEvent, $privacy, $language, $objectYouTube)
    {
        try{
            $this->setDefaultConfigurations($intialDate, $endDate, $titleEvent, $privacy, $language, $objectYouTube);

            $this->setLiveBroadcast()->setSnippet()->setLiveStatus()->creteEventBroadcast();
            $this->listVideo()->setSnippetVideo();
            $this->configurationLiveCDN()->insertLiveStream();
            $this->bindEvent();

            return $this->response;
        } catch ( Google_Service_Exception $e ) {
            throw new Exception($e->getMessage(), 1);
        } catch ( Google_Exception $e ) {
            throw new Exception($e->getMessage(), 1);
        } catch(Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }
}