<?php
    $ordercode = $_GET['order'];
    $list = json_decode($_GET['orderlist'], true);
    $id = json_encode($ordercode,JSON_UNESCAPED_UNICODE);
    $orderlist = json_encode($list,JSON_UNESCAPED_UNICODE);
    echo <<<EOM
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>test</title>
        <script type="module">
                import { PrintRequest } from "../samples/receiptPrinter/receiptPrinter.js";
                const orderid = $id;
                const orderlist = $orderlist;
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