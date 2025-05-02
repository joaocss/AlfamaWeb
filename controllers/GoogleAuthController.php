<?php
require_once '../vendor/autoload.php';
use Google\Service\Oauth2 as Google_Service_Oauth2;
session_start();

$clientID = '94065404697-45lttsflka1tpci4jdimc0r16002le2k.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/alfama-crud/controllers/GoogleAuthController.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($client);
        $data = $google_service->userinfo->get();

        $email = $data->email;
        $name = $data->name;
        $google_id = $data->id;

        require_once "../config/database.php";

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR google_id = :google_id");
        $stmt->execute([
            ':email' => $email,
            ':google_id' => $google_id
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, google_id) VALUES (:nome, :email, :google_id)");
            $stmt->execute([
                ':nome' => $name,
                ':email' => $email,
                ':google_id' => $google_id
            ]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
        }

        header('Location: ../views/atualizar.php');
        exit;
    }
} else {
    header('Location: ' . $client->createAuthUrl());
    exit;
}
