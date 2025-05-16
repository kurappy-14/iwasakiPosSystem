<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>呼び出しパネル(調理中)</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1 id="STORENAME">店名</h1>
    </header>

    <div class="container1">
        <div class="border">
            <div class="textborder1">
                <h2>調理中</h2>
            </div>
            <div class="list" id="cooking-list">
                <ul id="cooking-items"></ul>
            </div>
        </div>
    </div>

    <script>
        function updateLists() {
            let STORENAME;
            let windowHeight = window.innerHeight;
            document.documentElement.style.setProperty('--grid-object',`repeat(${Math.floor(windowHeight/150)}, 1fr)`);
            fetch('read.php')
                .then(response => response.json())
                .then(data => {
                    // 調理中のリストを更新
                    const cookingList = document.getElementById('cooking-items');
                    cookingList.innerHTML = ''; // クリアする
                    for (let i = 0; i < Math.min(data.cooking.length, Math.floor(windowHeight/150)*6); i++) {
                        const li = document.createElement('li');
                        li.textContent = data.cooking[i];
                        cookingList.appendChild(li);
                    }
                })
                .catch(error => console.error('Error:', error));
            fetch('../setting.json')
                .then(response => response.json())
                .then(data => {
                    STORENAME = data.STORENAME;
                    document.getElementById("STORENAME").textContent = STORENAME;
            })
        }

        // n秒ごとにリストを更新
        const updateInterval = 500;
        setInterval(updateLists, updateInterval);

        // 初回のリスト更新
        updateLists();
    </script>

</body>

</html>