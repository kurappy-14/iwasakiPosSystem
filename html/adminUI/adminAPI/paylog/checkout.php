<?php
header("Content-Type: application/json; charset=UTF-8");

//自動読み込み
require '../../../vendor/autoload.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['paymentid'];


//環境変数の取得
//settings_read.phpから取得
// ファイルのパスを指定して読み込む
$jsonFilePath = '../../../setting.json';

// ファイルが存在するかチェック
$jsonData = '';
if (file_exists($jsonFilePath)) {
    // ファイル内容を読み込む
    $jsonData = file_get_contents($jsonFilePath);
    // string → json に変換
    $jsonData = json_decode($jsonData, true);

} else {
    // エラーメッセージを返す
    echo json_encode(['error' => 'settings.json file not found']);
}
$TOKEN = $jsonData["environment"]["TOKEN"];



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