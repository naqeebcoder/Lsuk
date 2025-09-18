<?php

function getAccessToken_new($serviceAccountFile) {
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
    $credentials = new ServiceAccountCredentials($scopes, $serviceAccountFile);
    $accessToken = $credentials->fetchAuthToken();
    echo "getting access token ";
    return $accessToken['access_token'] ?? null;
}
$serviceAccountFile = 'firebase_file/lsuk-1530684014975-41cd4fc2c11d.json';  
$accessToken = getAccessToken_new($serviceAccountFile);
