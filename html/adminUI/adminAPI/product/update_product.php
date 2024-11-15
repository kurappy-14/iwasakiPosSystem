<?php
// コンテンツタイプをJSONに
header("Content-Type: application/json; charset=UTF-8");

// データベース接続情報
$servername = "mariaDB";
$username = "user";
$password = "password";
$dbname = "exampledb";

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続チェック
if ($conn->connect_error) {
    http_response_code(500);  // サーバーエラー
    echo json_encode(["message" => "データベース接続に失敗しました"]);
    exit;
}

// リクエストボディを取得してJSONデコード
$input = json_decode(file_get_contents("php://input"), true);

// 必須フィールドの確認
if (!isset($input['product_code'],$input['category_name'], $input['product_name'], $input['price'], $input['stockpile'])) {
    http_response_code(400);  // クライアントエラー
    echo json_encode(["message" => "必要なデータが不足しています"]);
    exit;
}

// 変数にデータを代入
$product_code = $conn->real_escape_string($input['product_code']);
$category_name = $conn->real_escape_string($input['category_name']);
$product_name = $conn->real_escape_string($input['product_name']);
$price = (int)$input['price'];
$stockpile = (int)$input['stockpile'];

// SQLで更新処理
$sql = "UPDATE products SET product_name = '$product_name', category_name = '$category_name', price = $price, stockpile = $stockpile WHERE product_code = '$product_code'";

if ($conn->query($sql) === TRUE) {
    http_response_code(200);  // 成功
    echo json_encode(["message" => "商品情報が更新されました"]);
} else {
    http_response_code(500);  // サーバーエラー
    echo json_encode(["message" => "更新に失敗しました: " . $conn->error]);
}

// データベース接続を閉じる
$conn->close();
?>
