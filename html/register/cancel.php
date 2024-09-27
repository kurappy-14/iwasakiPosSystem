<?php
require 'vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

$TOKEN = $_ENV['TOKEN'];

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$id = $data['paymentid'];

//クライアントの定義
$client = new \Square\SquareClient([
    'accessToken' => $TOKEN,
    'environment' => 'production'
]);

$api_response = $client->getTerminalApi()->cancelTerminalCheckout($id);

if ($api_response->isSuccess()) {
    $result = $api_response->getResult();
    echo json_encode(['status' => 'success', 'result' => $result]);
} else {
    $errors = $api_response->getErrors();
    echo json_encode(['status' => 'error', 'errors' => $errors]);
}