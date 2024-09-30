<?php
//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$referenceid = isset($data['referenceid']) ? $data['referenceid'] : 0;
$status = $data['status'];

$host = "hostname";
$user = "username";
$password = "password";
$db = "databasename";

try{
    //データベースと接続
    //$mysqli = new mysqli($host,$user,$password,$db);
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');

    $order_id = [];
    $reference_number = [];
    $provide_status = [];
    $date = [];

    $datalist = $mysqli->prepare("SELECT * FROM orders");
    //実行する
    $datalist->execute();
    $result = $datalist->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "Order ID: " . $row['order_id'] . ", reference_number: " . $row['reference_number'] . ", provide_status: " . $row['provide_status'] .", order_date: " . $row['order_date'] ."<br>";
        array_push($order_id,$row['order_id']);
        array_push($reference_number,$row['reference_number']);
        array_push($provide_status,$row["provide_status"]);
        array_push($date,$row["order_date"]);
    }

    //配列からIDを検索する見つかった場合は番号を返す。見つからなかった場合false
    $searchid = array_search($referenceid,$reference_number);

    if($searchid!==false){
        if($status==-1){
            $delete1 = $mysqli->prepare("DELETE FROM purchase WHERE order_id= ? ");
            $delete2 = $mysqli->prepare("DELETE FROM orders WHERE order_id= ? ");
            $delete1->bind_param('i',$order_id[$searchid]);
            $delete2->bind_param('i',$order_id[$searchid]);
            $delete1->execute();
            $delete2->execute();
        }else{
            $update = $mysqli->prepare("UPDATE orders SET provide_status = ? WHERE order_id = ?");
            $update->bind_param('ii',$status,$order_id[$searchid]);
            $update->execute();
        }
    }else{
        //データを挿入する
        $data = $mysqli->prepare("INSERT INTO orders VALUES(?,?,?,?)");
        $id = count($order_id) + 1;
        $Rid = "id";
        $section = 1;
        $date = date('Y-m-d');
        //?の部分に数値を代入
        $data->bind_param('ssss',$id,$Rid,$section,$date);
        $data->execute();
    }

    //データベースとの接続を解除
    $mysqli->close();
}catch(Exception $e){
    echo $e->getMessage();
}