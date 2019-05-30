<?php

namespace Laravel\Youtube\Video;

class Update
{
    private $videoId;
    private $snippet;

    public function getDataUpdate()
    {
        return ['videoId' => $this->videoId, 'snippet' => $this->snippet];
    }

    public function update($video, $youtube)
    {
        try {
            $returnData = $youtube->videos->update('status,snippet', $video);
            $this->videoId = $returnData['id'];
            $this->snippet = $returnData['snippet'];
        } catch (\Google_Service_Exception $e) {
            throw new Exception($e->getMessage());
        } catch (\Google_Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $this;
    }
}