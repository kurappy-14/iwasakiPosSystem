<?// setting.jsonの読み込み
$jsonFilePath = '../../../setting.json';
if (file_exists($jsonFilePath)) {
    $jsonData = file_get_contents($jsonFilePath);
    $settings = json_decode($jsonData, true);
    $categories = $settings['Category'];

    // 重みでソート（重みが0は最後に）
    usort($categories, function ($a, $b) {
        if ($a['weight'] == $b['weight']) {
            return 0;
        }
        return ($a['weight'] == 0) ? 1 : (($b['weight'] == 0) ? -1 : $a['weight'] - $b['weight']);
    });
} else {
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品追加</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="add-product-form">
        <h3>商品を追加</h3>
        <form id="add-product-form">
            <label for="product_code">商品コード:</label>
            <input type="text" id="product_code" name="product_code" required><br>

            <label for="product_name">商品名:</label>
            <input type="text" id="product_name" name="product_name" required><br>

            <!-- カテゴリ名（プルダウン） -->
            <label for="category_name">カテゴリ名:</label>
            <select id="category_name" name="category_name">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['name']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            
            <label for="price">価格:</label>
            <input type="number" id="price" name="price" required><br>

            <label for="stockpile">在庫数:</label>
            <input type="number" id="stockpile" name="stockpile" required><br>

            <button type="button" id="add-product-button">追加</button>
        </form>
        <div class="error-message" id="error-message"></div>
    </div>

    <script>
        // 商品追加の処理
        document.getElementById("add-product-button").addEventListener("click", () => {
            const productData = {
                product_code: document.getElementById("product_code").value,
                product_name: document.getElementById("product_name").value,
                category_name: document.getElementById("category_name").value,
                price: document.getElementById("price").value,
                stockpile: document.getElementById("stockpile").value
            };

            // 入力チェック
            if (!productData.product_code || !productData.product_name || !productData.category_name || !productData.price || !productData.stockpile) {
                document.getElementById("error-message").textContent = "すべての項目を入力してください。";
                return;
            }

            // fetchで商品追加のリクエストを送信
            fetch("add_product.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(productData)
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    //自身を開いたウィンドウが存在する場合
                    if ((window.opener && !window.opener.closed)) {
                        window.opener.location.reload();
                    }
                    if (data.message === "商品が追加されました") {
                        window.close();  // 成功したらウィンドウを閉じる
                    }
                })
                .catch(error => console.error("エラーが発生しました:", error));
        });
    </script>
</body>

</html>


<style>
    /* 商品追加フォームのスタイル */
    .add-product-form {
        background-color: #f9f9f9;

        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        width: 100%;
        max-width: 500px;

    }

    /* フォームのタイトル */
    .add-product-form h3 {
        font-size: 24px;
        color: #333;
        text-align: center;

    }

    /* ラベルとインプットのスタイル */
    .add-product-form label {
        display: block;
        font-size: 16px;

        color: #333;
    }

    .add-product-form input ,
    .add-product-form select {
        width: 100%;
        padding: 10px;
        margin-bottom: 14px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .add-product-form input:focus {
        border-color: #007BFF;
        outline: none;
    }

    /* 商品追加ボタン */
    .add-product-form button {
        background-color: #007BFF;
        color: #fff;
        padding: 12px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    .add-product-form button:hover {
        background-color: #0056b3;
    }

    /* フォームの入力が完了していない場合のエラーメッセージ */
    .add-product-form .error-message {
        color: red;
        font-size: 14px;
        text-align: center;
        margin-bottom: 10px;
    }
</style>