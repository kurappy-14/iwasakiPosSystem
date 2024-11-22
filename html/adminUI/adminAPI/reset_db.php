<?php
// コンテンツタイプをjsonに
header("Content-Type: application/json; charset=UTF-8");

// データベース接続情報
$servername = "mariaDB";
$username = "user";
$password = "password";
$dbname = "exampledb";



try {
    // PDOでデータベースに接続
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // データベースをリセット
    $resetSQL = <<<SQL
DROP DATABASE IF EXISTS $dbname;

CREATE DATABASE $dbname;
USE $dbname;

-- ordersテーブルの作成
CREATE TABLE orders (
    order_id INT PRIMARY KEY,
    call_number INT,
    payment_type VARCHAR(255),
    reference_number VARCHAR(255),
    provide_status INT,
    order_date DATETIME
);

-- productsテーブルの作成
CREATE TABLE products (
    product_code VARCHAR(255) PRIMARY KEY,
    category_name VARCHAR(255),
    product_name VARCHAR(255),
    price INT,
    stockpile INT
);

-- purchaseテーブルの作成
CREATE TABLE purchase (
    order_id INT,
    product_code VARCHAR(255),
    quantity INT,
    PRIMARY KEY (order_id, product_code),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_code) REFERENCES products(product_code)
);
SQL;

    // 実行
    $pdo->exec($resetSQL);
    echo json_encode(array("success" => true));

} catch (PDOException $e) {
    echo json_encode(array("success" => false, "message" => $e->getMessage()));
}