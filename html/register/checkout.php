<?php
require 'vendor/autoload.php';

$amount_money = new \Square\Models\Money();
$amount_money->setAmount(100);
$amount_money->setCurrency('JPY');

$device_options = new \Square\Models\DeviceCheckoutOptions('313CS145B3003834');

$checkout = new \Square\Models\TerminalCheckout($amount_money, $device_options);
$checkout->setPaymentType("CARD_PRESENT");

$idempotency_key = uniqid('', true);
$body = new \Square\Models\CreateTerminalCheckoutRequest($idempotency_key, $checkout);

$accessToken = 'EAAAl15Tg27RApW-t7v9QbzlWvpSYtO9_B2K1jJu9nIRKVBrOfwTX2PiXdkzw_q9';
$client = new \Square\SquareClient([
    'accessToken' => $accessToken,
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