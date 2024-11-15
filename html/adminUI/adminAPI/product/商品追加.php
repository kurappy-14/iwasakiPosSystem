            <!-- 商品追加用フォーム -->
            <div class="add-product-form">
                <h3>新しい商品を追加</h3>
                <form id="add-product-form">
                    <!-- 商品名 -->
                    <label for="new-product-name">商品名:</label>
                    <input type="text" id="new-product-name" name="product_name" required><br><br>

                    <!-- カテゴリ名 -->
                    <label for="new-category-name">カテゴリ名:</label>
                    <input type="text" id="new-category-name" name="category_name" required><br><br>

                    <!-- 価格 -->
                    <label for="new-price">価格:</label>
                    <input type="number" id="new-price" name="price" required><br><br>

                    <!-- 在庫数 -->
                    <label for="new-stockpile">在庫数:</label>
                    <input type="number" id="new-stockpile" name="stockpile" required><br><br>

                    <!-- 送信ボタン -->
                    <button type="button" id="add-product-button">商品を追加</button>
                </form>
            </div>