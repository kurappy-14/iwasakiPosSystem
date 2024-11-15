<?php
header('Content-Type: application/json');

// ファイルのパスを指定して読み込む
$jsonFilePath = '../../../setting.json';

// ファイルが存在するかチェック
if (file_exists($jsonFilePath)) {
    // ファイル内容を読み込む
    $jsonData = file_get_contents($jsonFilePath);
    
    // JSON形式で出力する
    echo $jsonData;
} else {
    // エラーメッセージを返す
    echo json_encode(['error' => 'settings.json file not found']);
}
