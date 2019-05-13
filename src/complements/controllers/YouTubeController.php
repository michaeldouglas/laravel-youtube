<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use YouTube;

class YouTubeController extends Controller
{
    public function auth()
    {
        return redirect()->to(YouTube::AuthUser());
    }

    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            throw new Exception('$_GET[\'code\'] is not set. Please re-authenticate.');
        }
        $token = Youtube::AuthCallback($request->get('code'));

        return $token;
    }
}
