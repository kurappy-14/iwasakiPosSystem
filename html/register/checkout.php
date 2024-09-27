<?php
require 'vendor/autoload.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$amount = $data['amount'];
$paymenttype = $data['type'];

Dotenv\Dotenv::createImmutable(__DIR__)->load();

$TOKEN = $_ENV['TOKEN'];
$device = $_ENV['DEVICE'];

$amount_money = new \Square\Models\Money();
$amount_money->setAmount($amount);
$amount_money->setCurrency('JPY');

$device_options = new \Square\Models\DeviceCheckoutOptions($device);

$checkout = new \Square\Models\TerminalCheckout($amount_money, $device_options);
$checkout->setPaymentType($paymenttype);

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