<?php
    $id = $_GET['id'];
    $Pid = json_encode($id,JSON_UNESCAPED_UNICODE);

    try{
        //データベースと接続
        //$mysqli = new mysqli($host,$user,$password,$db);
        $mysqli = new mysqli("mariaDB","user","password","exampledb");
    
        if( $mysqli->connect_errno ) {
            echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
        }
        //文字コードを設定
        $mysqli->set_charset('utf8');
        //purchaseテーブル
        $ordercode = [];
        $quantity = [];
        //productsテーブル
        $product_code = [];
        $product_name = [];
    
        $data = $mysqli->prepare("SELECT * FROM purchase WHERE order_id=?");
        $data->bind_param('s',$id);
        $data->execute();
        $result = $data->get_result();

        //purchaseテーブルのデータを配列に挿入
        while ($row = $result->fetch_assoc()) {
            array_push($ordercode,$row['product_code']);
            array_push($quantity,$row["quantity"]);
        }

        $result->free();
        $data->close();
        $data = $mysqli->prepare("SELECT * FROM products");
        $data->execute();
        $result = $data->get_result();

        //productテーブルのデータを配列に挿入
        while ($row = $result->fetch_assoc()) {
            array_push($product_code,$row['product_code']);
            array_push($product_name,$row['product_name']);
        }
    
        $result->free();
        $data->close();
        //データベースとの接続を解除
        $mysqli->close();

    } catch (Exception $e) {
        // エラーメッセージをJSONで返す
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
        exit;
    }
    $orderlist = [];
    foreach($ordercode as $index => $code){
        $idindex = array_search($code,$product_code);
        $orderlist[] = [
            'name' => $product_name[$idindex],
            'count' => $quantity[$index]
        ];
    }
    $Porderlist = json_encode($orderlist, JSON_UNESCAPED_UNICODE);

    echo <<<EOM
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>test</title>
        <script type="module">
                import { PrintRequest } from "../samples/receiptPrinter/receiptPrinter.js";
                const orderid = $Pid;
                const orderlist = $Porderlist;
                async function post(){
                    const req = new PrintRequest(orderid,orderlist);
                    await req.join();
                    await window.close();
                }
                post();
        </script>
    </head>
    <body>
    </body>
    </html>
    EOM;