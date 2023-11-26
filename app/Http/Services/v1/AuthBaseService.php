<?php

namespace App\Http\Services\v1;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

abstract class AuthBaseService
{
    /**
     * @param $credentials
     * @return mixed
     */
    public function _accessToken($credentials)
    {
        $username = $credentials['email'];
        $password = $credentials['password'];
        $oauthClient = $this->getClient();
        $data = [
            'grant_type' => 'password',
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'username' => $username,
            'password' => $password,
        ];
        //        $url = config('app.url').'/oauth/token';
        //        request()->request->add($data);
        //        $request = Request::create($url, 'POST');
        //        $content = Route::dispatch($request)->getContent();
        $request = app('request')->create('/oauth/token', 'POST', $data);
        $response = app('router')->prepareResponse($request, app()->handle($request));
        $data = $response->getContent();

        return json_decode($data, true);
    }

    /**
     * @param $token
     * @return object
     */
    public function _refreshToken($token)
    {
        $oauthClient = $this->getClient();
        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'refresh_token' => $token,
        ];
        $url = config('app.url') . '/oauth/token';
        request()->request->add($data);
        $request = Request::create($url, 'POST');
        $response = Route::dispatch($request);

        $statusCode = $response->status();
        $content = $response->getContent();

        if ($statusCode >= 200 && $statusCode <= 209) {
            return (object) ['data' => json_decode($content, true)];
        } else {
            return (object) ['data' => json_decode($content), 'code' => $statusCode];
        }
    }

    /**
     * @return mixed
     */
    abstract public function getClient();
}
