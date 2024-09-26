<?php
require 'vendor/autoload.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$id = $data['paymentid'];

//クライアントの定義
$accessToken = 'トークン';
$client = new \Square\SquareClient([
    'accessToken' => $accessToken,
    'environment' => 'production'
]);

$api_response = $client->getTerminalApi()->getTerminalCheckout($id);

if ($api_response->isSuccess()) {
    $result = $api_response->getResult();
    echo json_encode(['status' => 'success', 'result' => $result]);
} else {
    $errors = $api_response->getErrors();
    echo json_encode(['status' => 'error', 'errors' => $errors]);
}