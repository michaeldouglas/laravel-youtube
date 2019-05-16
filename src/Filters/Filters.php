<?php

namespace Laravel\Youtube\Filters;

use Carbon\Carbon;
use Exception;

trait Filters
{
    private $app;
    private $tags;

    public function __construct($app)
    {
        $this->app = $app;
    }

    private static function checkTags(String $tags)
    {
        return (!is_null($tags) && $tags !== "");
    }

    private function adjustTags($tags)
    {
        $this->tags = substr(str_replace([", ,", ",,", ", ,,", ",, ,,"], ",", $tags),0,498);
        $this->tags = (substr($tags, -1)==',') ? substr($tags,0,-1) : $tags;

        $this->tags = explode(',', $this->tags);
    }

    private function tags(String $tags)
    {
        if (static::checkTags($tags)) {

            $this->adjustTags($tags);

            return $this->tags;

        } else {
            return [];
        }
    }

    private function checkVideoExist(String $pathVideo)
    {
        if(!file_exists($pathVideo)) {
            throw new Exception("Video doest not exist. Path: {$pathVideo}");
        }
    }

    private function checkSnippet($snippet, array $data)
    {
        if (array_key_exists('title', $data)){
            $snippet->setTitle($data['title']);
        }
        if (array_key_exists('description', $data)){
            $snippet->setDescription($data['description']);
        }
        if (array_key_exists('tags', $data)){
            $snippet->setTags($data['tags']);
        }
        if (array_key_exists('category_id', $data)){
            $snippet->setCategoryId($data['category_id']);
        }

        return $snippet;
    }

    private function adjustDate(String $dateParam)
    {
        $dataFormated = Carbon::createFromFormat('Y-m-d H:i:s', $dateParam, $this->app->config->get('youtube.timezone'));
        $dataFormated = ($dataFormated < Carbon::now($this->app->config->get('youtube.timezone'))) ? Carbon::now($this->app->config->get('youtube.timezone')) : $dataFormated;
        return $dataFormated->toIso8601String();
    }
}