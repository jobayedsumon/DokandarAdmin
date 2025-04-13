<?php

namespace App\CentralLogics;

use Google\Client;
use Google\Exception;
use function Laravel\Prompts\error;

class FCM
{
    public static function sendMessage(string $fcmToken, string $title = '', string $body = '', array $data = []): bool
    {
        $response = null;

        try {
            $serviceAccountPath = storage_path('google-service-account.json');
            $projectId          = json_decode(file_get_contents($serviceAccountPath))->project_id;

            $url     = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
            $headers = [
                'Authorization: Bearer ' . self::getAuthToken($serviceAccountPath),
                'Content-Type: application/json',
            ];

            $message = [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
            ];

            if (!empty($data)) {
                $message['data'] = $data;
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));

            $response = curl_exec($ch);

            curl_close($ch);

            $result = json_decode($response, true);
            if (isset($result['name'])) {
                return true;
            }
        } catch (Exception $exception) {
            error_log('FCM Error: ' . $exception->getMessage(), 3, storage_path('logs/fcm.log'));
        }

        error_log('FCM Error: ' . print_r($response, true), 3, storage_path('logs/fcm.log'));

        return false;
    }

    /**
     * @throws Exception
     */
    private static function getAuthToken(string $serviceAccountPath): string
    {
        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();
        $token = $client->fetchAccessTokenWithAssertion();

        return $token['access_token'];
    }
}
