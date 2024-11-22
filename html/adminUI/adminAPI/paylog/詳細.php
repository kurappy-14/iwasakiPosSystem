<?php
// データベース接続情報
$host = "mariaDB";
$dbname = "exampledb";
$username = "user";
$password = "password";


// 接続処理
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "接続に失敗しました: " . $e->getMessage();
    exit();
}

// 特定のorder_idを指定
$order_id = $_GET['order_id']; //get で受け取る

// 1. 注文情報を取得
$query_order = "SELECT order_id, call_number, payment_type, provide_status, order_date ,reference_number 
                FROM orders 
                WHERE order_id = :order_id";
$stmt_order = $pdo->prepare($query_order);
$stmt_order->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt_order->execute();
$order = $stmt_order->fetch(PDO::FETCH_ASSOC);

// 注文情報が見つからない場合
if (!$order) {
    echo json_encode(["error" => "注文が見つかりません"]);
    exit();
}

// 2. 購入商品の情報を取得
$query_purchase = "SELECT p.product_name, p.price, pu.quantity, (p.price * pu.quantity) AS subtotal 
                   FROM purchase pu
                   JOIN products p ON pu.product_code = p.product_code
                   WHERE pu.order_id = :order_id";
$stmt_purchase = $pdo->prepare($query_purchase);
$stmt_purchase->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt_purchase->execute();
$products = $stmt_purchase->fetchAll(PDO::FETCH_ASSOC);

// 3. 合計金額の計算
$total_amount = 0;
foreach ($products as $product) {
    $total_amount += $product['subtotal'];
}

// 4. JSON形式でまとめる
$result = [
    "order" => $order,
    "products" => $products,
    "total_amount" => $total_amount
];

// 結果をJSON形式で出力して変数に保存

$json_data = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// echo $json_data;
// JSONデータをPHP配列にデコード
$data = json_decode($json_data, true);



// 商品の名前と個数をname,countのjsonにまとめる
$products = [];
foreach ($data['products'] as $product) {
    $products[] = [
        'name' => $product['product_name'],
        'count' => $product['quantity']
    ];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>注文情報の表示</title>
    <style>
        /* 全体のスタイル */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* 注文情報のスタイル */
        .order-info p {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .order-info strong {
            font-weight: bold;
        }

        /* 提供状況セクションのスタイル */
        .provide-status {
            display: flex;
            align-items: center;
        }

        .provide-status select {
            margin-right: 1px;
            margin-left: 5px;
            padding: 5px;
            font-size: 14px;
        }

        .provide-status button {
            padding: 6px 12px;
            font-size: 14px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .provide-status button:hover {
            background-color: #45a049;
        }

        /* テーブルのスタイル */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>

</head>

<body>

    <h2>注文情報</h2>
    <p><strong>注文ID:</strong> <?php echo htmlspecialchars($data['order']['order_id']); ?></p>
    <p><strong>呼び出し番号:</strong> <?php echo htmlspecialchars($data['order']['call_number']); ?></p>
    <p><strong>支払い方法:</strong>
        <?php if ($data['order']['payment_type'] == "現金"): ?>
            <?php echo htmlspecialchars($data['order']['payment_type']); ?>
        <?php else: ?>
            <?php echo htmlspecialchars($data['order']['payment_type']); ?> :
            <span id="ch">通信中...</span>
            <script>
                fetch('checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        paymentid: "<?php echo $data['order']['reference_number']; ?>"
                    })
                }).then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('ch').textContent = '決済完了';
                        } else {
                            document.getElementById('ch').textContent = '決済エラー';
                        }
                    })
                    .catch(error => {
                        document.getElementById('ch').textContent = 'エラーが発生しました';
                    });
            </script>
        <?php endif ?>
    </p>
    <div class="provide-status">
        <p><strong>提供状況:</strong>
        <form action="update_status.php" method="post">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($data['order']['order_id']); ?>">
            <select name="provide_status">
                <option value="1" <?php if ($data['order']['provide_status'] == "1")
                    echo "selected"; ?>>決済待ち</option>
                <option value="2" <?php if ($data['order']['provide_status'] == "2")
                    echo "selected"; ?>>準備中</option>
                <option value="3" <?php if ($data['order']['provide_status'] == "3")
                    echo "selected"; ?>>調理中</option>
                <option value="4" <?php if ($data['order']['provide_status'] == "4")
                    echo "selected"; ?>>提供待ち</option>
                <option value="5" <?php if ($data['order']['provide_status'] == "5")
                    echo "selected"; ?>>提供完了</option>
            </select>
            <button type="submit">更新</button>
        </form>
    </div>
    </p>
    <p><strong>注文日:</strong> <?php echo htmlspecialchars($data['order']['order_date']); ?></p>

    <h2>購入商品一覧</h2>

    <table>
        <thead>
            <tr>
                <th>商品名</th>
                <th>価格</th>
                <th>数量</th>
                <th>小計</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['products'] as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($product['price'])); ?>円</td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($product['subtotal'])); ?>円</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total">合計金額</td>
                <td><?php echo htmlspecialchars(number_format($data['total_amount'])); ?>円</td>
            </tr>
        </tfoot>
    </table>
    <?
    // ファイルのパスを指定して読み込む
    $jsonFilePath = '../../../setting.json';
    if (file_exists($jsonFilePath)) {
        // ファイル内容を読み込む
        $jsonData = file_get_contents($jsonFilePath);
        $jsonData = json_decode($jsonData, true);
    } else {
        $jsonData = json_encode(['error' => 'settings.json file not found']);
    }
    ?>
    <? if ($jsonData["printer"] == 1): ?>
        <script>
            function printReceipt(call_number, products) {
                fetch('reprint.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        call_number: call_number,
                        products: products
                    })
                }).then(response => response.json())
                    .then(data => {
                            alert('印刷を実行しました');
                    })
                    .catch(error => {
                        alert('エラーが発生しました');
                    });
            }

        </script>
        <button onclick='printReceipt(<?= $data["order"]["call_number"] ?>, <?= json_encode($products) ?>)'>レシートを印刷</button>
    <? endif ?>

</body>

</html>