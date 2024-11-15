<?php
// コンテンツタイプをjsonに
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
    die("Connection failed: " . $conn->connect_error);
}

// データを取得するSQLクエリ
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = array(); // データを保存する配列

// データを配列に追加
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// JSONに変換して出力
echo json_encode($products, JSON_UNESCAPED_UNICODE);

// データベース接続を閉じる
$conn->close();
?>
