<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>呼び出しパネル(完了)</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1 id="STORENAME">店名</h1>
    </header>

    <div class="container2">
        <div class="border">
            <div class="textborder2">
                <h2>完了</h2>
            </div>
            <div class="list" id="done-list">
                <ul id="done-items"></ul>
            </div>
        </div>
    </div>

    <script>
        //画面サイズに合わせて動的に行数を変更
        function changerow(){
            differenceheight = document.documentElement.clientHeight-1080;
            newdefaultrow = 10 + Math.floor(differenceheight/80);
            document.querySelector('ul').style.gridTemplateRows = `repeat(${newdefaultrow}, 1fr)`;
            fetch('../setting.json')
            .then(response => response.json())
            .then(data => {
                STORENAME = data.STORENAME;
                document.getElementById("STORENAME").textContent = STORENAME;
            })
            .catch(error => console.error('Error', error));
        }

        function updateLists() {
            fetch('read.php')
                .then(response => response.json())
                .then(data => {
                    // 完了のリストを更新
                    const doneList = document.getElementById('done-items');
                    doneList.innerHTML = ''; // クリアする
                    data.completed.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = item;
                        doneList.appendChild(li);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        // n秒ごとにリストを更新
        const updateInterval = 500; // 例えば5秒ごと
        setInterval(updateLists, updateInterval);
        setInterval(changerow, updateInterval);

        // 初回のリスト更新
        updateLists();
        changerow();
    </script>

</body>

</html>