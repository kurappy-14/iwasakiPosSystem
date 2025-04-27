<?php
// データベース接続
$pdo = new PDO("mysql:host=mariaDB;dbname=exampledb;charset=utf8", "user", "password");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// パラメータ取得
$order_id = $_GET['order_id'];
$status = $_GET['status'];

// ステータスを次に更新
$sql = "UPDATE orders SET provide_status = :status WHERE order_id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':status' => $status, ':order_id' => $order_id]);

echo "更新完了";
?>
