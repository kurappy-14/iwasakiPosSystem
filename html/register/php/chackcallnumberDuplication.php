<?php
header('Content-Type: application/json; charset=utf-8');
//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$callnumber = $data['callnumber'];
try{
    //データベースと接続
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }

    //文字コードを設定
    $mysqli->set_charset('utf8');

    //SQL文を設定
    $checkcallnumber = $mysqli->prepare("SELECT call_number FROM orders WHERE call_number = ?");
    $checkcallnumber->bind_param('s',$callnumber);
    $checkcallnumber->execute();
    $checkcallnumber->store_result();
    //データが存在するか確認
    if (0 < $checkcallnumber->num_rows) {
        $checkcallnumber2 = $mysqli->prepare("SELECT call_number FROM orders WHERE call_number = ? AND && provide_status != 5");
        $checkcallnumber2->bind_param('s',$callnumber);
        $checkcallnumber2->execute();
        $checkcallnumber2->store_result();
        if (0 < $checkcallnumber2->num_rows) {
            $data = ['status' => 'error'];
        }else{
            $data = ['status' => 'success'];
        }
    } else {
        $data = ['status' => 'success'];
    }

    //データベースとの接続を解除
    $mysqli->close();
    //product配列を返す
    echo json_encode($data);
} catch (Exception $e) {
    // エラーメッセージをJSONで返す
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}