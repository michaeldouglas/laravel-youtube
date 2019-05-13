<?php

/**
 * Group Router
 */
Route::group(['prefix' => config('youtube.routes.prefix')], function() {

    /**
     * Auth Router
     */
    Route::get(config('youtube.routes.authentication_uri'), 'YouTubeController@auth');

    /**
     * Redirect
     */
    Route::get(config('youtube.routes.redirect_uri'), 'YouTubeController@callback');
});