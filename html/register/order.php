<?php
header('Content-Type: application/json; charset=utf-8');
//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$orderid = $data['orderid'];
$amount = $data['amount'];

try{
    //データベースと接続
    //$mysqli = new mysqli($host,$user,$password,$db);
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');

    for ($i = 0; $i < count($amount); $i++) {
        if(0<$amount[$i]){
            //データを挿入する
            $data = $mysqli->prepare("INSERT INTO purchase VALUES(?,?,?)");
            //product_codeを抜き出す
            $productid = $mysqli->prepare("SELECT product_code FROM products ORDER BY product_code ASC LIMIT 1 OFFSET ?");
            $productid->bind_param('i',$i);
            $productid->execute();
            $productid_result = $productid->get_result();
            $product = $productid_result->fetch_assoc();
            $product_code = $product['product_code'];
            //?の部分に数値を代入
            $data->bind_param('ssi', $orderid, $product_code, $amount[$i]);
            $data->execute();
        }
    }
    
    //データベースとの接続を解除
    $mysqli->close();
    echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
} catch (Exception $e) {
    // エラーメッセージをJSONで返す
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit;
}