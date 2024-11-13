<?php
header('Content-Type: application/json; charset=utf-8');
//自動読み込み
require '../vendor/autoload.php';

//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$id = $data['referenceid'];

//.envを使用する
Dotenv\Dotenv::createImmutable(__DIR__)->load();
//定義した値を変数に代入
$TOKEN = $_ENV['TOKEN'];

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