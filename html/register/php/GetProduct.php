<?php
header('Content-Type: application/json');
try{
    //データベースと接続
    //$mysqli = new mysqli($host,$user,$password,$db);
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');

    //SQL文を設定
    $GetProduct = $mysqli->prepare("SELECT * FROM products");
    $GetProduct->execute(); //実行する
    $result = $GetProduct->get_result();    //結果をresultに格納
    $product = [];
    while ($row = $result->fetch_assoc()) {
        $product[] = $row;
    }

    //データベースとの接続を解除
    $mysqli->close();
    //product配列を返す
    echo json_encode(['status' => 'success', 'product' => $product]);
} catch (Exception $e) {
    // エラーメッセージをJSONで返す
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}