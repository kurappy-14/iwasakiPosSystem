<?php
// データベース接続情報
$host = "mariaDB";
$dbname = "exampledb";
$username = "user";
$password = "password";

// データベース接続
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

// ordersテーブルから情報を取得
$sql = "SELECT * FROM orders WHERE provide_status IN (2, 3, 4) ORDER BY order_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 各注文ごとに注文詳細を取得して追加
foreach ($orders as &$order) {
    $order_id = $order['order_id'];
    
    $detailSql = "
        SELECT products.product_name, products.price, purchase.quantity
        FROM purchase
        JOIN products ON purchase.product_code = products.product_code
        WHERE purchase.order_id = :order_id
    ";
    $detailStmt = $pdo->prepare($detailSql);
    $detailStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $detailStmt->execute();
    $orderDetails = $detailStmt->fetchAll(PDO::FETCH_ASSOC);

    // 注文詳細を各注文に追加
    $order['details'] = $orderDetails;
}

// JSONで結果を返す
header('Content-Type: application/json');
echo json_encode($orders);
