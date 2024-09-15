<?php
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
$table = "purchase";

// カラムとレコードを取得するSQLクエリ
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // カラム名を取得
    $fields = $result->fetch_fields();
    echo "<table border='1'><tr>";
    foreach ($fields as $field) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";

    // レコードを出力
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<td>" . $row[$field->name] . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>
