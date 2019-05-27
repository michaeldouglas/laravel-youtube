<?php

namespace Laravel\Youtube\Video;

class Search
{
    private $searchResponse;
    private $listVideos = [];

    private function getIframe(String $id)
    {
        return '<iframe width="100%" height="100%" class="embed-responsive-item" src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    }

    private function getPlayList(String $chanelId)
    {
        return 'https://www.youtube.com/playlist?list='.$chanelId.'';
    }

    private function getChannel(String $channelId)
    {
        return 'https://www.youtube.com/channel/'.$channelId.'';
    }

    private function filterSearch()
    {
        foreach ($this->searchResponse['items'] as $searchResult) {
            switch ($searchResult['id']['kind']) {
                case 'youtube#video':
                    $this->setReturnListVideos('video', 'videoId', $searchResult, $this->getIframe($searchResult['id']["videoId"]));
                    break;
                case 'youtube#channel':
                    $this->setReturnListVideos('channel', 'channelId', $searchResult, $this->getChannel($searchResult['id']["channelId"]));
                    break;
                case 'youtube#playlist':
                    $this->setReturnListVideos('playlist', 'playlistId', $searchResult, $this->getPlayList($searchResult['id']["playlistId"]));
                    break;
            }
        }
    }

    private function setReturnListVideos(String $key, String $keySearch, $searchResult, $value)
    {
        $this->listVideos[$key][] = ['title' => $searchResult['snippet']['title'], 'id' => $searchResult['id'][$keySearch], 'links_embed' => $value];
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