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




// カラムとレコードを取得するSQLクエリ
$sql = "SELECT 
    o.order_id, 
    SUM(p.price * pu.quantity) AS total_amount
FROM 
    orders o
INNER JOIN purchase pu ON o.order_id = pu.order_id
INNER JOIN products p ON pu.product_code = p.product_code
GROUP BY o.order_id;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // カラム名を取得
    $fields = $result->fetch_fields();
    echo "<table border='1'><tr>";
    foreach ($fields as $field) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";
    $sum =0;
    // レコードを出力
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<td>" . $row[$field->name] . "</td>";
            if($field->name == "total_amount"){
                $sum = $sum + $row[$field->name];
            }
        }
        echo "</tr>";
    }
    echo "</table>";

    echo $sum;
} else {
    echo "0 results";
}

$conn->close();
?>




