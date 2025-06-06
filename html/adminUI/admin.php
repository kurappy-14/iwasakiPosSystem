<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="admin.css">
    <link rel="stylesheet" type="text/css" href="../common.css">
    <script src="admin.js"></script>
    <meta charset="utf-8">
</head>

<body>
    <div class="header">
        <h1>Admin</h1>
        <nav id="top-menu" class="menu">
            <a href="#product-editor">商品の追加-編集</a>
            <a href="#category-editor">カテゴリの追加-編集</a>
            <a href="#payment-log">決済履歴-売り上げの表示</a>
            <a href="#function-toggle">機能の設定</a>
            <a href="#other-settings">その他</a>
        </nav>
    </div>

    <div class="container">

        <div id="product-editor" class="dashbord-content hidden">
            <h2>商品の追加-編集</h2>
            <table id="product-table">
                <thead>
                    <tr>
                        <td>商品名</td>
                        <td>商品コード</td>
                        <td>カテゴリ名</td>
                        <td>価格</td>
                        <td>在庫数</td>
                        <td>編集</td>
                    </tr>
                </thead>
                <tbody>
                    <!-- ここに商品の行が追加されるよ -->
                </tbody>
            </table>
            <button id="add-product" class="btn-green full-width">商品の追加</button>
            <script src="js/product.js"></script>
        </div>

        <div id="category-editor" class="dashbord-content hidden">
            <h2>カテゴリの追加-編集</h2>
            <ul id="category-list"></ul>
            <script src="js/category.js"></script>
            <button id="save-categories" class="btn-blue full-width" onclick="saveCategories()">全て保存</button>
            <!-- カテゴリ追加フォーム -->
            <h3>新しいカテゴリを追加</h3>
            <div id="new-category-form">
                <input type="text" id="new-category-name" class="category-add-item" placeholder="カテゴリ名" />
                <input type="number" id="new-category-weight" class="category-add-item" placeholder="重み" />
            </div>
            <button id="add-category" class="btn-green full-width" onclick="addCategory()">カテゴリ追加</button>
        </div>

        <div id="payment-log" class="dashbord-content hidden">
            <h2>決済履歴-売り上げ</h2>
            <div class="dashbord-content-header">
                <div id="sum"></div>
            </div>
            <script src="js/paylog.js"></script>
        </div>

        <div id="function-toggle" class="dashbord-content hidden">
            <!-- 支払い方法のチェックボックスエリア -->
            <h2>機能の設定</h2>
            <!-- 更新ボタン -->
            <button id="updateSettings" class="btn-green" onclick="updateSettings()">更新</button>
            <div id="settings-container">
                <div id="settings-A">
                    <div id="paytype-settings">
                        <label>支払い方法:</label>
                        <label><input type="checkbox" id="cash" /> 現金</label>
                        <label><input type="checkbox" id="JCB" /> JCB</label>
                        <label><input type="checkbox" id="Visa" /> Visa</label>
                        <label><input type="checkbox" id="QUICPay" /> QUICPay</label>
                        <label><input type="checkbox" id="iD" /> iD</label>
                        <label><input type="checkbox" id="IC" /> IC</label>
                        <label><input type="checkbox" id="PayPay" /> PayPay</label>
                        <label><input type="checkbox" id="others" /> その他</label>
                    </div>
                    <hr>
                    <!-- プリンター設定のチェックボックス -->
                    <div id="printer-setting">
                        <label>プリンター設定:</label>
                        <label><input type="checkbox" id="printer" /> サーマルプリンターを使用</label>
                    </div>
                    <hr>
                    <!-- 
                                        "enableVoiceVoxSpeak": true,
                                        "isZundamon": true -->
                                        <label for="enableVoiceVoxSpeak"><input type="checkbox" id="enableVoiceVoxSpeak" />音声合成</label>
                                        <label><input type="checkbox" id="isZundamon" />ずんだもん</label>
                </div>

                <!-- 環境設定のセクション -->
                <div id="settings-B">
                    <label for="store-name">店舗名:</label>
                    <input type="text" id="store-name" class="input full-width" name="store-name" />

                    <label for="token">TOKEN: <button id="token-toggle" class="hide" type="button"
                            onclick="toggleVisibility('token')">表示</button></label>
                    <div class="input-container">
                        <input type="password" id="token" class="input full-width" name="token" />
                    </div>

                    <label for="device-id">DEVICE ID: <button id="device-id-toggle" class="hide" type="button"
                            onclick="toggleVisibility('device-id')">表示</button></label>
                    <div class="input-container">
                        <input type="password" id="device-id" class="input full-width" name="device-id" />
                    </div>

                    <label for="printer-ip">Printer IP:</label>
                    <input type="text" id="printer-ip" class="input full-width" name="printer-ip" />
                    <a id="printer-ip-test" href="javascript:void(0)" onclick="testPrinterIP()">印刷テスト</a>
                </div>



            </div>

        </div>

        <div id="other-settings" class="dashbord-content hidden">
            <h2>csv出力</h2>
            <ul>
                <li><a href="adminAPI/csv_ouput/ordercsv.php">ordercsv</a></li>
                <li><a href="adminAPI/csv_ouput/productscsv.php">productscsv</a></li>
                <li><a href="adminAPI/csv_ouput/purchasecsv.php">purchasecsv</a></li>
            </ul>
            <hr>
            <h2>設定ファイルのエクスポート</h2>
            <a href="../setting.json" download="export.json">設定ファイルのエクスポート</a>
            <h2>設定ファイルのインポート</h2>
            <input type="file" id="import" accept=".json" />
            <button id="import-button" onclick="importSetting()">インポート</button>
            <hr>
            <details>
                <summary>データベースのリセット</summary>
                <button id="reset-db" onclick="resetDB()">実行</button>
            </details>

        </div>

        <br>
    </div>
    <div class="footer">
        <p>Admin Panel</p>
    </div>
</body>

</html>