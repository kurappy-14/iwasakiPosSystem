<?php
try{
    //データベースと接続
    //$mysqli = new mysqli($host,$user,$password,$db);
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');

    //データを保存する配列を宣言
    //ordersテーブル
    $id = [];
    $reference_number = [];
    $provide_status = [];
    $date = [];
    //purchaseテーブル
    $orderid = [];
    $ordercode = [];
    $quantity = [];
    //productsテーブル
    $product_code = [];
    $product_name = [];
    $price = [];
    $test;

    $data1 = $mysqli->prepare("SELECT * FROM orders WHERE provide_status=?");
    $number = 1;
    $data1->bind_param('i',$number);
    $data1->execute();
    $result1 = $data1->get_result();
    
    //ordersテーブルのデータを配列に挿入
    while ($row = $result1->fetch_assoc()) {
        array_push($id,$row['order_id']);
        array_push($reference_number,$row['reference_number']);
        array_push($provide_status,$row["provide_status"]);
        array_push($date,$row["order_date"]);
    }
    $result1->free();
    $data1->close();
    $data2 = $mysqli->prepare("SELECT * FROM purchase");
    $data2->execute();
    $result2 = $data2->get_result();

    //purchaseテーブルのデータを配列に挿入
    while ($row = $result2->fetch_assoc()) {
        array_push($orderid,$row['order_id']);
        array_push($ordercode,$row['product_code']);
        array_push($quantity,$row["quantity"]);
    }

    $result2->free();
    $data2->close();
    $data3 = $mysqli->prepare("SELECT * FROM products");
    $data3->execute();
    $result3 = $data3->get_result();

    //productテーブルのデータを配列に挿入
    while ($row = $result3->fetch_assoc()) {
        array_push($product_code,$row['product_code']);
        array_push($product_name,$row['product_name']);
        array_push($price,$row["price"]);
    }
    
    $result3->free();
    $data3->close();
    //データベースとの接続を解除
    $mysqli->close();
    
    //response内容
    $response = [
        'status' => 'success',
        'orders' => [
            'id' => $id,
            'reference_number' => $reference_number,
            'provide_status' => $provide_status,
            'date' => $date,
        ],
        'purchase' => [
            'orderid' => $orderid,
            'ordercode' => $ordercode,
            'quantity' => $quantity,
        ],
        'products' => [
            'product_code' => $product_code,
            'product_name' => $product_name,
            'price' => $price,
        ],
    ];

    //JSONで返却
    echo json_encode($response);

} catch (Exception $e) {
    // エラーメッセージをJSONで返す
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit;
}