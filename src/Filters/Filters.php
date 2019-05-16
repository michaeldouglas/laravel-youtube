<?php

namespace Laravel\Youtube\Filters;

use Carbon\Carbon;

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

    private function adjustDate(String $dateParam)
    {
        $dataFormated = Carbon::createFromFormat('Y-m-d H:i:s', $dateParam, $this->app->config->get('youtube.timezone'));
        $dataFormated = ($dataFormated < Carbon::now($this->app->config->get('youtube.timezone'))) ? Carbon::now($this->app->config->get('youtube.timezone')) : $dataFormated;
        return $dataFormated->toIso8601String();
    }
}