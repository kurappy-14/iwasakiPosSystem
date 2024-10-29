<?php
header('Content-Type: application/json; charset=utf-8');
//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$product = $data['product'];
$price = $data['price'];

try{
    //データベースと接続
    //$mysqli = new mysqli($host,$user,$password,$db);
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');

    $reset = $mysqli->prepare("DELETE FROM products");
    //実行する
    $reset->execute();

    for ($i = 0; $i < count($product); $i++) {
        //データを挿入する
        $data = $mysqli->prepare("INSERT INTO products VALUES(?,?,?)");
        $productid = "P" . $i;
        $productname = $product[$i];
        $productprice = $price[$i];
        //?の部分に数値を代入
        $data->bind_param('sss',$productid,$productname,$productprice);
        $data->execute();
    }
    
    //データベースとの接続を解除
    $mysqli->close();
    echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
} catch (Exception $e) {
    // エラーメッセージをJSONで返す
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}