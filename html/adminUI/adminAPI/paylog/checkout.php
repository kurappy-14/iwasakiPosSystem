<?php
header("Content-Type: application/json; charset=UTF-8");

//自動読み込み
require '../../../vendor/autoload.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['paymentid'];

//.envを使用する
Dotenv\Dotenv::createImmutable(__DIR__)->load();
//定義した値を変数に代入
$TOKEN = $_ENV['TOKEN'];

//クライアントの定義
$client = new \Square\SquareClient([
    'accessToken' => $TOKEN,
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