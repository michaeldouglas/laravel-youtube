<?php

namespace Laravel\Youtube;

use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Google_Service_YouTube_Video;

use Laravel\Youtube\Configuration\Setup;
use Laravel\Youtube\Database\Database;
use Laravel\Youtube\Filters\Filters;
use Laravel\Youtube\Video\Upload;
use Exception;

class Youtube
{

    use Filters;

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
     * Google Client
     *
     * @var Setup
     */
    protected $setup;

    /**
     * DB Client
     *
     * @var Database
     */
    protected $db;

    /**
     * Upload Client
     *
     * @var Upload
     */
    protected $upload;

    /**
     * Google YouTube
     *
     * @var \Google_Service_YouTube
     */
    protected $youtube;

    public function __construct($app, Google_Client $client)
    {
        $this->app = $app;

        $this->setup = new Setup($app, $client);

        $this->client = $this->setup->getClient();

        $this->db = new Database();

        $this->upload = new Upload();

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
     * List Events Broadcasts
     * @throws Exception
     * @return array
     */
    public function listEventsBroadcasts()
    {
        try {
            return $this->setup->listEventsBroadcasting($this->youtube);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }

    /**
     * Delete video by ID
     * @param String $id
     * @return boolean
     * @throws Exception
     */
    public function delete(String $id)
    {
        $this->userToken();

        if (!$this->checkExistVideo($id)) {
            throw new Exception("Not found video: {$id}");
        }

        return (bool)$this->youtube->videos->delete($id);
    }

    public function createEventRTMP(String $intialDate, String $endDate, String $titleEvent, String $privacy = 'unlisted', $language = 'Portuguese (Brazil)', $tags = 'michael,laravel-youtube')
    {
        $this->userToken();

        $liveBroadcast = $this->setup->getClientBroadcasting($intialDate, $endDate, $titleEvent, $privacy, $language, $tags, $this->youtube);

        return $liveBroadcast;
    }

    /**
     * Upload video for YouTube
     * @param String $pathLocalVideo
     * @param array $dataVideo
     * @param string $privacyVideo
     * @return Upload
     * @throws Exception
     */
    public function uploadVideo(String $pathLocalVideo, array $dataVideo = [], $privacyVideo = 'public')
    {
        try {
            $this->checkVideoExist($pathLocalVideo);

            $this->userToken();

            $video = $this->getVideoYouTube($dataVideo, $privacyVideo);

            $this->upload->upload($this->client, $this->youtube, $video, $pathLocalVideo);

            return $this->upload;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }

    private function getVideoYouTube(array $data, String $privacyStatus, $id = false)
    {
        $snippetObject = new Google_Service_YouTube_VideoSnippet();
        $snippet = $this->checkSnippet($snippetObject, $data);

        $status = new Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = $privacyStatus;

        $video = new Google_Service_YouTube_Video();

        if ($id) {
            $video->setId($id);
        }

        $video->setSnippet($snippet);
        $video->setStatus($status);

        return $video;
    }

    /**
     * Get Details Based Id Video
     * @param String $id
     * @return array
     */
    public function getDetailsVideo(String $id)
    {
        $this->userToken();

        return $this->youtube->videos->listVideos('snippet', ['id' => $id])[0]['snippet'];
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
            if ($this->app->config->get('youtube.redirect_auth')) {
                $uri = $this->client->getRedirectUri();
                header("Location: $uri");
            } else {
                throw new \Exception('An access token is required.');
            }
        }

        if ($this->client->isAccessTokenExpired()) {

            if (array_key_exists('refresh_token', $accessToken)) {
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