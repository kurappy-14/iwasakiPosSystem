<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>キッチン管理画面</title>
    <link rel="stylesheet" href="css/style.css">
<script src="js/kitchen.js"></script>
</head>
<body>

    <h1>キッチン管理画面</h1>

    <div class="container">
        <div id="preparing" class="status-area">
            <h2>準備中</h2>
            <div id="preparing-orders"></div>
        </div>

        <div id="cooking" class="status-area">
            <h2>調理中</h2>
            <div id="cooking-orders"></div>
        </div>

        <div id="waiting-for-pickup" class="status-area">
            <h2>提供待ち</h2>
            <div id="pickup-orders"></div>
        </div>
    </div>


</body>

</html>
