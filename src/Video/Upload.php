<?php

namespace Laravel\Youtube\Video;

use Google_Http_MediaFileUpload;
use Google_Service_Exception;
use Google_Exception;
use Exception;

class Upload
{
    private $media;
    private $id;
    private $snippet;


    const SIZE = 1 * 1024 * 1024;

    public function getIdVideo()
    {
        return $this->id;
    }

    public function getSnippet()
    {
        return $this->snippet;
    }

    public function upload($client, $youtube, $video, $pathLocalVideo)
    {
        try {
            $client->setDefer(true);

            $insert = $youtube->videos->insert('status,snippet', $video);

            $this->media = new Google_Http_MediaFileUpload(
                $client,
                $insert,
                'video/*',
                null,
                true,
                self::SIZE
            );

            $this->media->setFileSize(filesize($pathLocalVideo));

            $status = false;
            $handle = fopen($pathLocalVideo, "rb");

            while (!$status && !feof($handle)) {
                $chunk = fread($handle, self::SIZE);
                $status = $this->media->nextChunk($chunk);
            }

            fclose($handle);

            $client->setDefer(false);

            $this->id = $status['id'];

            $this->snippet = $status['snippet'];

        } catch (Google_Service_Exception $e) {
            throw new Exception($e->getMessage());
        } catch (Google_Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $this;
    }
}