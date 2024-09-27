<?php
//自動読み込み
require 'vendor/autoload.php';

//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$amount = $data['amount'];
$paymenttype = $data['type'];

//.envを使用する
Dotenv\Dotenv::createImmutable(__DIR__)->load();
//定義した値を変数に代入
$TOKEN = $_ENV['TOKEN'];
$device = $_ENV['DEVICE'];

//支払情報の設定
$amount_money = new \Square\Models\Money();
$amount_money->setAmount($amount);
$amount_money->setCurrency('JPY');

//ここからよくわからん
$device_options = new \Square\Models\DeviceCheckoutOptions($device);

$checkout = new \Square\Models\TerminalCheckout($amount_money, $device_options);
$checkout->setPaymentType($paymenttype);

//キーの作成的な
$idempotency_key = uniqid('', true);
$body = new \Square\Models\CreateTerminalCheckoutRequest($idempotency_key, $checkout);

//クライアントの定義
$client = new \Square\SquareClient([
    'accessToken' => $TOKEN,
    'environment' => 'production'
]);

$api_response = $client->getTerminalApi()->createTerminalCheckout($body);

if ($api_response->isSuccess()) {
    $result = $api_response->getResult();
    echo json_encode(['status' => 'success', 'result' => $result]);
} else {
    $errors = $api_response->getErrors();
    echo json_encode(['status' => 'error', 'errors' => $errors]);
}