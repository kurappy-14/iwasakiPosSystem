<?php
header('Content-Type: application/json; charset=utf-8');
//変数の利用
$input = file_get_contents("php://input");
$data = json_decode($input, true);
//javascriptの変数をphpの変数に代入
$orderid = $data['orderid'];
$order = $data['order'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>test</title>
    <script type="module">
        import { PrintRequest } from "./receiptPrinter.js"
        document.getElementById("Print").addEventListener("click", async() =>{
            const req = new PrintRequest(<?php echo $order;?>,<?php echo json_encode($order);?>);
            document.getElementById("result").innerText= "request  sent..." ;
            await req.join();
            document.getElementById("result").innerText= req.status ;
        });
    </script>
</head>
<body>
</body>
</html>