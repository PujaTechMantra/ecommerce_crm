<?php

namespace App\Services;

use Google\Client;
use GuzzleHttp\Exception\RequestException;

class FCMService
{
    protected $client;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID'); // set in .env
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google/service-account.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function sendPushNotification(string $deviceToken, string $title, string $body, array $data = [])
    {
        $httpClient = $this->client->authorize();

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data,
            ]
        ];
        
        try {
            $response = $httpClient->post($url, [
                'json' => $message,
            ]);
            return json_decode((string) $response->getBody(), true);

        } catch (RequestException $e) {
            // dd($e->getMessage());
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse()
                    ? (string) $e->getResponse()->getBody()
                    : null,
            ];
        }
    }
}
