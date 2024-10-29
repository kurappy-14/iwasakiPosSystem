<?php
// レスポンスタイプをapplication/jsonに設定
header("Content-Type: application/json; charset=UTF-8");


$servername = "mariaDB"; // docker-composeで定義されたサービス名
$username = "user"; // MariaDBのユーザー名
$password = "password"; // MariaDBのパスワード
$dbname = "exampledb"; // MariaDBのデータベース名

// 接続を作成
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続をチェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// テーブル名を指定
$table = "orders";

// カラムとレコードを取得するSQLクエリ(調理中)
$sql = "SELECT order_id FROM $table where provide_status = 2 or provide_status = 3";
$result = $conn->query($sql);

// レコードを格納する配列
$records = array();

if ($result->num_rows > 0) {
    // カラム名を取得
    $fields = $result->fetch_fields();

    // レコードを出力
    while($row = $result->fetch_assoc()) {
        $record = array();
        foreach ($fields as $field) {
            $record[]= $row[$field->name];
        }
        $records[] = $record;
    }
}

// カラムとレコードを取得するSQLクエリ(調理完了)
$sql = "SELECT order_id FROM $table where provide_status = 4";
$result = $conn->query($sql);

// レコードを格納する配列
$records2 = array();

if ($result->num_rows > 0) {
    // カラム名を取得
    $fields = $result->fetch_fields();

    // レコードを出力
    while($row = $result->fetch_assoc()) {
        $record = array();
        foreach ($fields as $field) {
            $record[]= $row[$field->name];
        }
        $records2[] = $record;
    }
}

$response = array(
    "cooking" => $records,
    "completed" => $records2
);

// レスポンスを出力
echo json_encode($response, JSON_UNESCAPED_UNICODE);


$conn->close();

?>
