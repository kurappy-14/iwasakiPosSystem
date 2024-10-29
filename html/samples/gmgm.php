<?php
require 'vendor/autoload.php';
use Square\SquareClientBuilder;
use Square\Authentication\BearerAuthCredentialsBuilder;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Models\Builders\CreateTerminalCheckoutRequestBuilder;
use Square\Models\Builders\TerminalCheckoutBuilder;
use Square\Models\Builders\MoneyBuilder;
use Square\Models\Builders\DeviceCheckoutOptionsBuilder;
use Square\Models\Currency;
///home/usec/iwasakiPosSystem/html/vendor/square/square/src/Models/Builders/CreateTerminalCheckoutRequestBuilder.php

//.envを使用する
Dotenv\Dotenv::createImmutable(__DIR__)->load();
//定義した値を変数に代入
$TOKEN = $_ENV['TOKEN'];

$client = SquareClientBuilder::init()
  ->bearerAuthCredentials(
      BearerAuthCredentialsBuilder::init(
          $TOKEN
        )
  )
  ->environment(Environment::PRODUCTION)
  ->build();

$terminalApi = $client->getTerminalApi();
//$idempotency_key = uniqid('',true);
$idempotency_key = uniqid('',true);
$body = CreateTerminalCheckoutRequestBuilder::init(
    $idempotency_key,
    TerminalCheckoutBuilder::init(
        MoneyBuilder::init()
            ->amount(500)
            ->currency(Currency::JPY)
            ->build(),
        DeviceCheckoutOptionsBuilder::init(
            '313CS145B3003834'
        )->build()
    )->build()
    
)->build();

$apiResponse = $terminalApi->createTerminalCheckout($body);

if ($apiResponse->isSuccess()) {
    $createTerminalCheckoutResponse = $apiResponse->getResult();
} else {
    $errors = $apiResponse->getErrors();
}

// Getting more response information
var_dump($apiResponse->getStatusCode());
var_dump($apiResponse->getHeaders());
?>