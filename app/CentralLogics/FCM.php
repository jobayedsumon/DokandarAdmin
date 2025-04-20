<?php

namespace App\CentralLogics;

use Google\Client;
use Google\Exception;
use function Laravel\Prompts\error;

class FCM
{
    public static function sendMessage(array $data, string $fcmToken = null, string $topic = null): bool
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
                'notification' => [
                    'title'              => $data['title'] ?? '',
                    'body'               => $data['body'] ?? $data['description'] ?? '',
                    'image'              => $data['image'] ?? '',
                ],
                'data' => [
                    'title'              => $data['title'] ?? '',
                    'body'               => $data['body'] ?? $data['description'] ?? '',
                    'image'              => $data['image'] ?? '',
                    'order_id'           => isset($data['order_id']) ? "{$data['order_id']}" : '',
                    'title_loc_key'      => $data['title_loc_key'] ?? '',
                    'body_loc_key'       => $data['body_loc_key'] ?? '',
                    'type'               => $data['type'] ?? '',
                    'conversation_id'    => isset($data['conversation_id']) ? "{$data['conversation_id']}" : '',
                    'sender_type'        => $data['sender_type'] ?? '',
                    'module_id'          => isset($data['module_id']) ? "{$data['module_id']}" : '',
                    'order_type'         => $data['order_type'] ?? '',
                    'zone_id'            => isset($data['zone_id']) ? "{$data['zone_id']}" : '',
                    'is_read'            => '0',
                    'icon'               => $data['icon'] ?? 'new',
                    'sound'              => $data['sound'] ?? 'notification.wav',
                    'android_channel_id' => isset($data['android_channel_id']) ? "{$data['android_channel_id']}" : 'dokandar',
                    'token'              => $data['token'] ?? '',
                    'channel'            => $data['channel'] ?? '',
                    'callerId'           => isset($data['callerId']) ? "{$data['callerId']}" : '',
                    'callerType'         => $data['callerType'] ?? '',
                    'callerName'         => $data['callerName'] ?? '',
                    'callerImage'        => $data['callerImage'] ?? '',
                ],
            ];

            if (isset($data['click_action'])) {
                $message['data']['notification'] = $data['click_action'];
            }

            if ($fcmToken) {
                $message['token'] = $fcmToken;
            }

            if ($topic) {
                $message['topic'] = $topic;
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
