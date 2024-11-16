<?php
header('Content-Type: application/json; charset=utf-8');
//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$printer = $data['printer'];
$referenceid = $data['referenceid'];
$callnumber = $data['callnumber'];
$status = $data['status'];
$paytype = $data['paytype'];

$host = "hostname";
$user = "username";
$password = "password";
$db = "databasename";

try{
    //データベースと接続
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');

    $order_id = [];
    $reference_number = [];

    $datalist = $mysqli->prepare("SELECT * FROM orders");
    //実行する
    $datalist->execute();
    $result = $datalist->get_result();

    while ($row = $result->fetch_assoc()) {
        array_push($order_id,$row['order_id']);
        array_push($reference_number,$row['reference_number']);
    }

    //配列からIDを検索する見つかった場合は番号を返す。見つからなかった場合false
    $searchid = array_search($referenceid,$reference_number);

    if($searchid!==false){
        if($status===-1){   //-1ならデータベースから削除する
            $delete1 = $mysqli->prepare("DELETE FROM purchase WHERE order_id= ? ");
            $delete2 = $mysqli->prepare("DELETE FROM orders WHERE order_id= ? ");
            $delete1->bind_param('i',$order_id[$searchid]);
            $delete2->bind_param('i',$order_id[$searchid]);
            $delete1->execute();
            $delete2->execute();
        }else{  //存在していて1か2なら状態を更新する
            $update = $mysqli->prepare("UPDATE orders SET provide_status = ? WHERE order_id = ?");
            $update->bind_param('ii',$status,$order_id[$searchid]);
            $update->execute();
            $update2 = $mysqli->prepare("UPDATE orders SET call_number = ? WHERE order_id = ?");
            $update2->bind_param('ii',$callnumber,$order_id[$searchid]);
            $update2->execute();
        }
    }else{
        //データを挿入する
        if($printer){
            $data = $mysqli->prepare("INSERT INTO orders VALUES(?,?,?,?,?,?)");
            $id = end($order_id) + 1;
            $callnumber = $id;
            $date = date('Y-m-d H:i:s');
            //?の部分に数値を代入
            $data->bind_param('ssssss',$id,$callnumber,$paytype,$referenceid,$status,$date);
            $data->execute();
            //orderidを受け渡す
            $response = ['status' => 'success', 'id' => $id, 'callid' => $callnumber];
        }else{
            $data = $mysqli->prepare("INSERT INTO orders VALUES(?,?,?,?,?,?)");
            $id = end($order_id) + 1;
            $date = date('Y-m-d H:i:s');
            //?の部分に数値を代入
            $data->bind_param('ssssss',$id,$callnumber,$paytype,$referenceid,$status,$date);
            $data->execute();
            //orderidを受け渡す
            $response = ['status' => 'success', 'id' => $id, 'callid' => $callnumber];
        }
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