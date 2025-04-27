<?php
// データベース接続情報
$host = "mariaDB";
$dbname = "exampledb";
$username = "user";
$password = "password";

// データベースに接続
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "接続に失敗しました: " . $e->getMessage();
    exit();
}

// POSTデータを受け取る
$order_id = $_POST['order_id'];
$provide_status = $_POST['provide_status'];

// 提供状況の更新クエリ
$update_query = "UPDATE orders SET provide_status = :provide_status WHERE order_id = :order_id";
$stmt = $pdo->prepare($update_query);
$stmt->bindParam(':provide_status', $provide_status, PDO::PARAM_INT);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

try {
    $stmt->execute();
    // 更新が成功した場合
    echo "提供状況が更新されました。";
    // 元のページに戻る

} catch (PDOException $e) {
    // 更新が失敗した場合
    echo "提供状況の更新に失敗しました: " . $e->getMessage();
}

?>
<script>
    //自身を開いたウィンドウが存在する場合
    if ((window.opener && !window.opener.closed)) {
        window.opener.location.reload();
    }
    window.location.href = '詳細.php?order_id=<?php echo $order_id; ?>';
</script>