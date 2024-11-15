<?php
header('Content-Type: application/json');

// 設定ファイルのパス
$jsonFilePath = '../../../setting.json';

// POSTされたデータを受け取る
$data = json_decode(file_get_contents('php://input'), true);

// 設定が正しく受け取られていれば更新
if ($data) {
    // 既存の設定を読み込む
    if (file_exists($jsonFilePath)) {
        $jsonData = file_get_contents($jsonFilePath);
        $settings = json_decode($jsonData, true);

        // 新しいデータで設定を更新
        $settings['paytype'] = $data['paytype'];
        $settings['printer'] = $data['printer'];
        $settings['STORENAME'] = $data['STORENAME'];  // STORENAMEの更新
        $settings['environment'] = $data['environment'];

        // 更新されたデータを設定ファイルに書き込む
        file_put_contents($jsonFilePath, json_encode($settings, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'settings.json file not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
