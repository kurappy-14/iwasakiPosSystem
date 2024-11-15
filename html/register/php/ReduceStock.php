<?php
header('Content-Type: application/json; charset=utf-8');
//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$MENU = $data['MENU'];
$quantity = $data['quantity'];
try{
    //データベースと接続
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');
        for($i=0;$i<count($MENU);$i++){
            $newquantity = $MENU[$i]['stockpile'] - $quantity[$i];
            $productcode = $MENU[$i]['product_code'];
            $update = $mysqli->prepare("UPDATE products SET stockpile = ? WHERE product_code = ?");
            $update->bind_param('is',$newquantity,$productcode);
            $update->execute();
        }
    //データベースとの接続を解除
    $mysqli->close();
}catch(Exception $e){
    echo $e->getMessage();
}

if (empty($response)) {
    $response = ['status' => 'no data'];
}

echo json_encode($response);