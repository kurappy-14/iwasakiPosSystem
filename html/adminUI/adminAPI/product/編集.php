<?php
// 商品コードをURLのパラメータから取得
$product_code = $_GET['product_code'] ?? '';

// データベース接続情報
$servername = "mariaDB";
$username = "user";
$password = "password";
$dbname = "exampledb";

// データベースに接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーチェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 商品情報の取得クエリ
$sql = "SELECT * FROM products WHERE product_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_code);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// setting.jsonの読み込み
$jsonFilePath = '../../../setting.json';
if (file_exists($jsonFilePath)) {
    $jsonData = file_get_contents($jsonFilePath);
    $settings = json_decode($jsonData, true);
    $categories = $settings['Category'];

    // 重みでソート（重みが0は最後に）
    usort($categories, function($a, $b) {
        if ($a['weight'] == $b['weight']) {
            return 0;
        }
        return ($a['weight'] == 0) ? 1 : (($b['weight'] == 0) ? -1 : $a['weight'] - $b['weight']);
    });
} else {
    $categories = [];
}
?>

<?php if ($product): ?>
    <h2>商品情報の編集：（<? echo $product_code ?>）</h2>
    <form id="edit-form">
        <!-- 商品名 -->
        <label for="product_name">商品名:</label>
        <input type="text" id="product_name" name="product_name"
            value="<?= htmlspecialchars($product['product_name']) ?>"><br><br>

        <!-- カテゴリ名（プルダウン） -->
        <label for="category_name">カテゴリ名:</label>
        <select id="category_name" name="category_name">
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['name']) ?>"
                    <?= $product['category_name'] === $category['name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- 価格 -->
        <label for="price">価格:</label>
        <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>"><br><br>

        <!-- 在庫数 -->
        <label for="stockpile">在庫数:</label>
        <input type="number" id="stockpile" name="stockpile" value="<?= htmlspecialchars($product['stockpile']) ?>"><br><br>

        <!-- アップデートボタン -->
        <button type="button" id="update-button">アップデート</button>
    </form>

    <script>
        // アップデートボタンのイベントリスナー
        document.getElementById("update-button").addEventListener("click", () => {
            // 編集フォームからデータを取得
            const productData = {
                product_code: "<?= $product_code ?>",
                product_name: document.getElementById("product_name").value,
                category_name: document.getElementById("category_name").value,
                price: document.getElementById("price").value,
                stockpile: document.getElementById("stockpile").value
            };

            // fetchでPOSTリクエストを送信
            fetch("update_product.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(productData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);  // サーバーからのメッセージを表示

                        //自身を開いたウィンドウが存在する場合
                        if ((window.opener && !window.opener.closed)) {
                            window.opener.location.reload();
                        }
                    }
                })
                .catch(error => console.error("エラーが発生しました:", error));
        });
    </script>
<?php else: ?>
    <p>商品が見つかりませんでした。</p>
<?php endif; ?>

<?php
// データベース接続を閉じる
$stmt->close();
$conn->close();
?>




<style>
    /* ページ全体のスタイル */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
        color: #333;
    }

    /* コンテナ */
    .container {
        width: 80%;
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* 見出し */
    h2 {
        text-align: center;
        color: #5a5a5a;
        font-size: 24px;
        margin-bottom: 0px;
    }

    /* フォーム */
    form {
        display: flex;
        flex-direction: column;
    }

    /* ラベル */
    label {
        font-size: 16px;
        margin-bottom: 8px;
    }

    /* 入力フィールド */
    input[type="text"],
    input[type="number"],
    select {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    /* ボタン */
    button {
        padding: 10px 20px;
        font-size: 16px;
        color: white;
        background-color: #4CAF50;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #45a049;
    }

    /* 商品コード表示 */
    .product-code {
        text-align: center;
        font-size: 18px;
        color: #777;
        margin-top: 20px;
    }

    /* エラーメッセージ */
    .error-message {
        color: red;
        font-size: 16px;
        margin-top: 20px;
    }
</style>
