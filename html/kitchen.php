<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文管理システム</title>
    <style>
        /* レイアウト調整用のスタイル */
        .container {
            display: flex;
            justify-content: space-around;
        }

        .order-column {
            width: 30%;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .order {
            border: 1px solid #999;
            margin-bottom: 10px;
            padding: 10px;
        }

        .order h3 {
            margin: 0 0 5px;
        }

        .order ul {
            padding-left: 20px;
        }

        .button-group {
            margin-top: 10px;
        }

        .button-group button {
            margin-right: 5px;
        }
    </style>
</head>

<body>

    <h1>注文管理システム</h1>

    <div class="container">
        <?
        $servername = "mariaDB"; // docker-composeで定義されたサービス名
        $username = "user"; // MariaDBのユーザー名
        $password = "password"; // MariaDBのパスワード
        $dbname = "exampledb"; // MariaDBのデータベース名
        
        // 接続を作成
        $conn = new mysqli($servername, $username, $password, $dbname);

        // 接続をチェック
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        ?>
        <!-- 待機中の注文 -->
        <div class="order-column" id="pending-orders">
            <h2>待機中の注文</h2>
            <? 
            $query = "SELECT order_id FROM orders WHERE provide_status = 2;";
            $result = $conn->query($query);
            

            ?>

            <? for ($i = 0; $i < $result->num_rows; $i++) { ?>
                <? 
                    $row = $result->fetch_assoc();
                    $order_id = $row['order_id'];
                    
                    ?>

                <div class="order" id="">
                    <h3>注文番号: <? echo $order_id ?> </h3>
                    <?
                            $query = "SELECT p.product_name, pu.quantity FROM purchase pu JOIN products p ON pu.product_code = p.product_code WHERE pu.order_id = $order_id;";
                            $result2 = $conn->query($query);
                    ?>
                    <ul>
                        <? for ($j = 0; $j < $result2->num_rows; $j++) { ?>
                            <? 
                                $row2 = $result2->fetch_assoc();
                                $product_code = $row2['product_name'];
                                $quantity = $row2['quantity'];
                            ?>
                            <li><? echo $product_code ?>: <? echo $quantity ?> </li>
                        <? } ?>
                    </ul>
                    <div class="button-group">
                        <button onclick="moveToNextStatus('')">調理中へ</button>
                        <button onclick="cancelOrder('')">キャンセル</button>
                    </div>
                </div>
            <? } ?>
        </div>

        <!-- 調理中の注文 -->
        <div class="order-column" id="cooking-orders">
            <h2>調理中の注文</h2>
            <? 
            $query = "SELECT order_id FROM orders WHERE provide_status = 3;";
            $result = $conn->query($query);
            

            ?>

            <? for ($i = 0; $i < $result->num_rows; $i++) { ?>
                <? 
                    $row = $result->fetch_assoc();
                    $order_id = $row['order_id'];
                    
                    ?>

                <div class="order" id="">
                    <h3>注文番号: <? echo $order_id ?> </h3>
                    <?
                            $query = "SELECT p.product_name, pu.quantity FROM purchase pu JOIN products p ON pu.product_code = p.product_code WHERE pu.order_id = $order_id;";
                            $result2 = $conn->query($query);
                    ?>
                    <ul>
                        <? for ($j = 0; $j < $result2->num_rows; $j++) { ?>
                            <? 
                                $row2 = $result2->fetch_assoc();
                                $product_code = $row2['product_name'];
                                $quantity = $row2['quantity'];
                            ?>
                            <li><? echo $product_code ?>: <? echo $quantity ?> </li>
                        <? } ?>
                    </ul>
                    <div class="button-group">
                        <button onclick="moveToNextStatus('')">調理中へ</button>
                        <button onclick="cancelOrder('')">キャンセル</button>
                    </div>
                </div>
            <? } ?>
        </div>

        <!-- 受け取り待ちの注文 -->
        <div class="order-column" id="waiting-pickup-orders">
            <h2>受け取り待ちの注文</h2>
            <? 
            $query = "SELECT order_id FROM orders WHERE provide_status = 4;";
            $result = $conn->query($query);
            

            ?>

            <? for ($i = 0; $i < $result->num_rows; $i++) { ?>
                <? 
                    $row = $result->fetch_assoc();
                    $order_id = $row['order_id'];
                    
                    ?>

                <div class="order" id="">
                    <h3>注文番号: <? echo $order_id ?> </h3>
                    <?
                            $query = "SELECT p.product_name, pu.quantity FROM purchase pu JOIN products p ON pu.product_code = p.product_code WHERE pu.order_id = $order_id;";
                            $result2 = $conn->query($query);
                    ?>
                    <ul>
                        <? for ($j = 0; $j < $result2->num_rows; $j++) { ?>
                            <? 
                                $row2 = $result2->fetch_assoc();
                                $product_code = $row2['product_name'];
                                $quantity = $row2['quantity'];
                            ?>
                            <li><? echo $product_code ?>: <? echo $quantity ?> </li>
                        <? } ?>
                    </ul>
                    <div class="button-group">
                        <button onclick="moveToNextStatus('')">調理中へ</button>
                        <button onclick="cancelOrder('')">キャンセル</button>
                    </div>
                </div>
            <? } ?>
    </div>

    <script>
        // ステータスを次に移行する関数
        function moveToNextStatus(orderId) {
            alert(orderId + ' を次のステータスに移動します');
            // 実際のステータス変更処理はここに書くよ！
        }

        // キャンセル処理
        function cancelOrder(orderId) {
            alert(orderId + ' をキャンセルします');
            // キャンセル処理はここに追加するよ！
        }

        // 受け取り完了処理
        function completeOrder(orderId) {
            alert(orderId + ' の受け取りが完了しました');
            // 完了処理をここに追加するんだ！
        }
    </script>

</body>

</html>