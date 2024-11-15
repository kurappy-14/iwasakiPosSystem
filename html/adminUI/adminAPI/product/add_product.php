<?php
// add_product_api.php
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
    http_response_code(500);
    echo json_encode(["message" => "データベース接続に失敗しました"]);
    exit;
}

// リクエストボディを取得してJSONデコード
$input = json_decode(file_get_contents("php://input"), true);

// 必須フィールドの確認
if (!isset($input['product_code'], $input['product_name'], $input['category_name'], $input['price'], $input['stockpile'])) {
    http_response_code(400);
    echo json_encode(["message" => "必要なデータが不足しています"]);
    exit;
}

// 変数にデータを代入
$product_code = $conn->real_escape_string($input['product_code']);
$product_name = $conn->real_escape_string($input['product_name']);
$category_name = $conn->real_escape_string($input['category_name']);
$price = (int)$input['price'];
$stockpile = (int)$input['stockpile'];

// 商品コードが重複していないか確認
$sql_check = "SELECT * FROM products WHERE product_code = '$product_code'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows > 0) {
    http_response_code(400);  // 重複している場合は400エラー
    echo json_encode(["message" => "この商品コードはすでに使用されています"]);
    exit;
}

// SQLで商品を追加
$sql = "INSERT INTO products (product_code, product_name, category_name, price, stockpile) 
        VALUES ('$product_code', '$product_name', '$category_name', $price, $stockpile)";

if ($conn->query($sql) === TRUE) {
    http_response_code(200);
    echo json_encode(["message" => "商品が追加されました"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "商品追加に失敗しました: " . $conn->error]);
}

// データベース接続を閉じる
$conn->close();
?>
