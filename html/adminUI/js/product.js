// fetchでPHPからデータを取得
fetch("adminAPI/product/get_product.php")  // ここでPHPファイルのパスを指定してね
    .then(response => response.json())
    .then(data => {
        const tableBody = document.querySelector("#product-table tbody");

        // データをテーブルに追加
        data.forEach(product => {
            const row = document.createElement("tr");

            // 商品名
            const productNameCell = document.createElement("td");
            productNameCell.textContent = product.product_name;
            row.appendChild(productNameCell);

            // 商品コード
            const productCodeCell = document.createElement("td");
            productCodeCell.textContent = product.product_code;
            row.appendChild(productCodeCell);

            // カテゴリID
            const categoryNameCell = document.createElement("td");
            categoryNameCell.textContent = product.category_name;
            row.appendChild(categoryNameCell);

            // 価格
            const priceCell = document.createElement("td");
            priceCell.textContent = product.price;
            row.appendChild(priceCell);

            // 在庫数
            const stockpileCell = document.createElement("td");
            stockpileCell.textContent = product.stockpile;
            row.appendChild(stockpileCell);

            // 更新ボタン
            const updateCell = document.createElement("td");
            const updateButton = document.createElement("b");
            updateButton.style.cursor = "pointer";
            updateButton.style.color = "blue";
            updateButton.textContent = "編集";
            updateButton.addEventListener("click", () => {
                // 更新ボタンのクリック時の処理
                window.open("adminAPI/product/編集.php?product_code=" + product.product_code, null, "width=400, height=500");

            });
            updateCell.appendChild(updateButton);
            row.appendChild(updateCell);

            // テーブルに行を追加
            tableBody.appendChild(row);
        });
    })
    .catch(error => console.error("データの取得エラー:", error));



document.getElementById("add-product").addEventListener("click", () => {
    window.open("adminAPI/product/追加.php", null, "width=400, height=500");
});