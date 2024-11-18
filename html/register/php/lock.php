<?php
header('Content-Type: application/json');
try {
    //データベースと接続
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }
    // テーブルをロック
    $query = "LOCK TABLES products WRITE";
    $mysqli->query($query);
    
   //データベースとの接続を解除
   $mysqli->close();
   $data = ["message" => "successfully"];
   echo json_encode($data);
}catch(Exception $e){
    echo $e->getMessage();
}