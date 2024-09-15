<?php
// レスポンスタイプをapplication/jsonに設定
header("Content-Type: application/json; charset=UTF-8");

// MariaDB接続情報
$servername = "mariaDB"; // docker-composeで定義されたサービス名
$username = "user";      // MariaDBのユーザー名
$password = "password";  // MariaDBのパスワード
$dbname = "exampledb";   // MariaDBのデータベース名

// データベース接続を作成
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続チェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQLクエリ作成 
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

// データを取得
$data = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}else{
    echo "0 results";
}



// JSON形式で出力
echo json_encode($data);

// データベース接続を閉じる
$conn->close();
?>
