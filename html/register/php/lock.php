<?php
header('Content-Type: application/json');
try {
    //データベースと接続
    $mysqli = new mysqli("mariaDB","user","password","exampledb");

    if( $mysqli->connect_errno ) {
        echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }
    //文字コードを設定
    $mysqli->set_charset('utf8');
    // テーブルをロック
    $flagFile = 'stop.txt'; // 停止指示を保存するファイル

    // フラグファイルが存在する場合、処理を終了
    if (file_exists($flagFile)) {
        unlink($flagFile); // フラグファイルを削除
    }
    $mysqli->begin_transaction();
    $mysqli->query('LOCK TABLES products READ');
    while(!(file_exists($flagFile))){ // フラグファイルの存在を確認
        sleep(0.5);
    }
    unlink($flagFile); // フラグファイルを削除
    exit();
}catch(Exception $e){
    echo $e->getMessage();
}