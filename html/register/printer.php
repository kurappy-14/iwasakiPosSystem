<?php
    //変数の利用
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    //javascriptの変数をphpの変数に代入
    $orderid = $data['orderid'];
    $order = $data['orderlist'];
    $idtmp = json_encode($orderid, JSON_UNESCAPED_UNICODE);
    $ordertmp = json_encode($order, JSON_UNESCAPED_UNICODE);
    //無理！！なんだこれ！！消え去れ!!!!!!!!
    echo <<<EOM
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>test</title>
        <script type="module">
                import { PrintRequest } from "../samples/receiptPrinter/receiptPrinter.js";
                const orderid = $idtmp;
                const orderlist = $ordertmp;
                async function post(){
                    const req = new PrintRequest(orderid,orderlist);
                    await req.join();
                }
                post();
        </script>
    </head>
    <body>
    </body>
    </html>
    EOM;