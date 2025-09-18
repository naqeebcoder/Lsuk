<?php
require "googleauth/vendor/autoload.php";
$serviceAccountFile = 'firebase_file/lsuk-1530684014975-41cd4fc2c11d.json';  
// $accessToken = getAccessToken_new($serviceAccountFile);

function getCachedAccessToken($serviceAccountFile) {
    $cacheFile = __DIR__ . '/access_token_cache.json';

    if (file_exists($cacheFile)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached && isset($cached['access_token'], $cached['expires_at'])) {
            if ($cached['expires_at'] > time()) {
                return $cached['access_token'];
            }
        }
    }

    // Fetch new token
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
    $credentials = new Google\Auth\Credentials\ServiceAccountCredentials($scopes, $serviceAccountFile);
    $tokenData = $credentials->fetchAuthToken();
    $token = $tokenData['access_token'] ?? null;

    if ($token) {
        file_put_contents($cacheFile, json_encode([
            'access_token' => $token,
            'expires_at' => time() + 3500 // slightly less than 1 hour to be safe
        ]));
    }

    return $token;
}

getCachedAccessToken($serviceAccountFile);

