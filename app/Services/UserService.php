<?php

namespace App\Services;

use Illuminate\Http\Request;

class UserService
{
    public static function get($token) {
        if(!$token) {
            return (object) [
                'success' => false,
                'error' => 'Missing token.'
            ];
        }
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', 'https://igp-auth.lce-ci.com/api/auth/user', [
                'headers' => [
                    'Authorization' => $token,
                    'Accept' => 'application/json',
                ]
            ]);
            $user = json_decode($response->getBody()->getContents())->user;
            $data = (object) [
                'success' => true,
                'user' => $user
            ];
            return $data;

        } catch (ClientException $e) {
            return (object) ['success' => false, 'error' => $e];
        }
    }
    public static function getAdmin($role,$url,$module) {

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', 'https://igp-auth.lce-ci.com/api/auth/admin/'.$role.'/'.$url.'/'.$module, [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            $user = json_decode($response->getBody()->getContents())->user;
            $data = (object) [
                'success' => true,
                'user' => $user
            ];
            return $data;

        } catch (ClientException $e) {
            return (object) ['success' => false, 'error' => $e];
        }
    }
    public static function getUserSeller() {

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', 'https://igp-auth.lce-ci.com/api/auth/userSeller', [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            $user = json_decode($response->getBody()->getContents())->data;
            $data = (object) [
                'success' => true,
                'users' => $user
            ];
            return $data;

        } catch (ClientException $e) {
            return (object) ['success' => false, 'error' => $e];
        }
    }
}
