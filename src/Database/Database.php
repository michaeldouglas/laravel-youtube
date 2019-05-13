<?php

namespace Laravel\Youtube\Database;

use Illuminate\Support\Facades\DB;

class Database
{
    public function saveToken($token)
    {
        return DB::table('youtubeTokens')->insert([
            'access_token' => json_encode($token),
            'created_at' => (new \DateTime())->setTimestamp($token['created']),
        ]);
    }

    public function getToken()
    {
        $token = DB::table('youtubeTokens')
            ->latest('created_at')
            ->first();

        return $this->verifyToken($token);
    }

    private function verifyToken($token)
    {
        return $token ? (is_array($token) ? $token['access_token'] : $token->access_token ) : null;
    }
}