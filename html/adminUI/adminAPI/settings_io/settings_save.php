<?php
header('Content-Type: application/json');

// POSTデータを取得
$data = json_decode(file_get_contents('php://input'), true);

// データが正しく送られてきたか確認
if (isset($data['Category'])) {
    // settings.jsonのパス
    $jsonFilePath = '../../../setting.json';

    // ファイルが存在するか確認
    if (file_exists($jsonFilePath)) {
        // ファイルの内容を読み込む
        $jsonData = file_get_contents($jsonFilePath);
        $jsonArray = json_decode($jsonData, true);

        // 新しいCategoryデータをセット
        $jsonArray['Category'] = $data['Category'];

        // 更新したデータをJSONとして保存
        file_put_contents($jsonFilePath, json_encode($jsonArray, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

        // 成功した場合
        echo json_encode(['success' => true]);
    } else {
        // settings.jsonが存在しない場合
        echo json_encode(['error' => 'settings.json file not found']);
    }
} else {
    // 必要なデータがない場合
    echo json_encode(['error' => 'Invalid data']);
}
?>
