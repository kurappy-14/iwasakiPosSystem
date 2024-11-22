<?php
// 設定ファイルのパス（ここは必要に応じて変更してね）
$settingsFilePath = '../../../setting.json';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '無効なリクエスト方法です。']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'ファイルのアップロードに失敗しました。']);
    exit;
}

$uploadedFile = $_FILES['file']['tmp_name'];

// アップロードされたファイルを読み込む
$newSettings = file_get_contents($uploadedFile);

if (!$newSettings) {
    echo json_encode(['success' => false, 'message' => 'ファイルの内容が読めませんでした。']);
    exit;
}

// JSON形式か確認する
if (json_decode($newSettings) === null) {
    echo json_encode(['success' => false, 'message' => '無効なJSON形式です。']);
    exit;
}

// 設定ファイルを書き換える
if (file_put_contents($settingsFilePath, $newSettings) === false) {
    echo json_encode(['success' => false, 'message' => '設定ファイルの保存に失敗しました。']);
    exit;
}

echo json_encode(['success' => true, 'message' => '設定ファイルが更新されました。']);
