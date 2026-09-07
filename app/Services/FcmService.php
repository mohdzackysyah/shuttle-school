<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Helper to encode base64url
     */
    private static function base64UrlEncode($text)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    /**
     * Generate OAuth2 Access Token for Google FCM HTTP v1 API
     */
    public static function getAccessToken()
    {
        $jsonPath = storage_path('app/firebase-service-account.json');
        if (!file_exists($jsonPath)) {
            Log::error("FCM Service Account JSON not found at: {$jsonPath}");
            return null;
        }

        try {
            $sa = json_decode(file_get_contents($jsonPath), true);
            $now = time();

            $header = self::base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = self::base64UrlEncode(json_encode([
                'iss' => $sa['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600
            ]));

            $signature = '';
            openssl_sign("$header.$payload", $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = "$header.$payload." . self::base64UrlEncode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->successful()) {
                return [
                    'token' => $response->json('access_token'),
                    'project_id' => $sa['project_id'] ?? 'hepigo-8b544'
                ];
            }

            Log::error("FCM OAuth Token Error: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("FCM OAuth Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send Push Notification to User via FCM HTTP v1 API
     */
    public static function sendToUser($userId, $title, $body, array $data = [])
    {
        $user = User::find($userId);
        if (!$user || empty($user->fcm_token)) {
            Log::info("FCM Send skipped for User #{$userId}: No FCM token registered yet.");
            return false;
        }

        return self::send($user->fcm_token, $title, $body, $data);
    }

    /**
     * Dispatch FCM HTTP v1 Push Notification
     */
    public static function send($fcmToken, $title, $body, array $data = [])
    {
        if (empty($fcmToken)) {
            return false;
        }

        $auth = self::getAccessToken();
        if (!$auth) {
            return false;
        }

        $accessToken = $auth['token'];
        $projectId = $auth['project_id'];

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $stringData = [];
        foreach (array_merge(['title' => $title, 'body' => $body, 'click_action' => 'FLUTTER_NOTIFICATION_CLICK'], $data) as $k => $v) {
            $stringData[(string)$k] = (string)$v;
        }

        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => (string)$title,
                    'body' => (string)$body,
                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'channel_id' => 'hepigo_alerts_v1',
                        'sound' => 'default',
                        'default_vibrate_timings' => true,
                        'notification_priority' => 'PRIORITY_MAX',
                        'visibility' => 'PUBLIC'
                    ]
                ],
                'data' => $stringData
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            Log::info("FCM HTTP v1 Send Result [Status {$response->status()}]: " . $response->body());
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("FCM Send Exception: " . $e->getMessage());
            return false;
        }
    }
}
