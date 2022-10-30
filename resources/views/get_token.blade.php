<?php

//print_r(__DIR__);
//die();

use Laravel\Sanctum\Http\Controllers;
require __DIR__.'\\..\\vendor\\amocrm\\examples\\bootstrap.php';
include_once '\\examples\\bootstrap.php';

session_start();


/** Соберем данные для запроса */
$data = [
    'client_id' => 'e28c6755-2f28-4023-b6eb-91639ec77fd0',
    'client_secret' => 'ffE1WVyxB9EhLwCUwcP8rldUkcz7a9w8EOkoX7aW4rIpLJLqSqwTZbOjMEHEYY5q',
    'grant_type' => 'authorization_code',
    'code' => '',
    'redirect_uri' => 'https://voyadgerodin.amocrm.ru/',
];


$apiClient = new \AmoCRM\Client\AmoCRMApiClient($data['client_id'], $data['client_secret'], $data['redirect_uri']);

if (isset($_GET['referer'])) {
    $apiClient->setAccountBaseDomain($_GET['referer']);
}


if (!isset($_GET['code'])) {
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth2state'] = $state;
    if (isset($_GET['button'])) {
        echo $apiClient->getOAuthClient()->getOAuthButton(
            [
                'title' => 'Установить интеграцию',
                'compact' => true,
                'class_name' => 'className',
                'color' => 'default',
                'error_callback' => 'handleOauthError',
                'state' => $state,
            ]
        );
        die;
    } else {
        $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message',
        ]);
        header('Location: ' . $authorizationUrl);
        die;
    }
} elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

/**
 * Ловим обратный код
 */
try {
    $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

    if (!$accessToken->hasExpired()) {
        saveToken([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => $apiClient->getAccountBaseDomain(),
        ]);
    }
} catch (Exception $e) {
    die((string)$e);
}

$ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

printf('Hello, %s!', $ownerDetails->getName());
