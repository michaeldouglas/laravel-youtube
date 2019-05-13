<?php

/**
 * Group Router
 */
Route::group(['prefix' => config('youtube.routes.prefix')], function() {

    /**
     * Auth Router
     */
    Route::get(config('youtube.routes.authentication_uri'), function()
    {
        return redirect()->to(YouTube::AuthUser());
    });

    /**
     * Redirect
     */
    Route::get(config('youtube.routes.redirect_uri'), function(Illuminate\Http\Request $request)
    {
        if(!$request->has('code')) {
            throw new Exception('$_GET[\'code\'] is not set.');
        }
        $token = Youtube::AuthCallback($request->get('code'));

        Youtube::saveTokenCallBack($token);

        return redirect(config('youtube.routes.redirect_back_uri', '/'));
    });
});