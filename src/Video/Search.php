<?php

namespace Laravel\Youtube\Video;

class Search
{
    private $searchResponse;
    private $listVideos = [];

    private function filterSearch()
    {
        foreach ($this->searchResponse['items'] as $searchResult) {
            switch ($searchResult['id']['kind']) {
                case 'youtube#video':
                    $this->setReturnListVideos('video', 'videoId', $searchResult);
                    break;
                case 'youtube#channel':
                    $this->setReturnListVideos('channel', 'channelId', $searchResult);
                    break;
                case 'youtube#playlist':
                    $this->setReturnListVideos('playlist', 'playlistId', $searchResult);
                    break;
            }
        }
    }

    private function setReturnListVideos(String $key, String $keySearch, $searchResult)
    {
        $this->listVideos[$key][] = ['title' => $searchResult['snippet']['title'], 'id' => $searchResult['id'][$keySearch]];
    }

    public function search(\Google_Service_YouTube $objectYouTube, String $query, String $maxResults)
    {
        $this->searchResponse = $objectYouTube->search->listSearch('id,snippet', [
            'q' => $query,
            'maxResults' => $maxResults
        ]);

        $this->filterSearch();

        return $this->listVideos;
    }
}