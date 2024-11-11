<?
echo $_GET["order_id"];


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

// 注文番号を取得
$order_id = $_GET["order_id"];

// SQLクエリを準備
$sql = "UPDATE orders SET provide_status = 3 WHERE order_id = ?";

// プリペアドステートメントを作成
$stmt = $conn->prepare($sql);

// パラメータをバインド
$stmt->bind_param("i", $order_id);

// クエリを実行
if ($stmt->execute()) {
    echo "Order status updated successfully!";
} else {
    echo "Error updating record: " . $stmt->error;
}

// 接続を閉じる
$stmt->close();
$conn->close();







echo '<script>setTimeout("link()",0);
function link(){
location.href="kitchen.php";
}</script>';
// スクリプトを終了させる
exit();

