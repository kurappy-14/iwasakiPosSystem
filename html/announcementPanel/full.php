<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>呼び出しパネル</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1 id="STORENAME">店名</h1>
    </header>

    <div class="container">
        <div class="border">
            <div class="textborder1">
                <h2>調理中</h2>
            </div>
            <div class="list" id="cooking-list">
                <ul id="cooking-items"></ul>
            </div>
        </div>
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
        let STORENAME;
        function changerow(){
            let windowHeight = window.innerHeight;
            calcHeight = Math.floor(windowHeight / 110);
            fontHeight = Math.floor(windowHeight / 100);
            if(windowHeight<1000){
                calcHeight = Math.floor(windowHeight / 120);
                fontHeight = Math.floor(windowHeight / 110);
            }else if(2000 < windowHeight){
                calcHeight = Math.floor(windowHeight / 150);
                fontHeight = Math.floor(windowHeight / 40);
            }else if(1500 < windowHeight){
                fontHeight = Math.floor(windowHeight/80);
            }
            //フォントサイズ変更
            document.documentElement.style.setProperty('--li-font-size',70+fontHeight+'px');
            document.querySelector('ul').style.gridTemplateRows = `repeat(${calcHeight}, 1fr)`;
            document.getElementById('done-items').style.gridTemplateRows = `repeat(${calcHeight}, 1fr)`;
            document.documentElement.style.setProperty('--h1-font-size',(62+calcHeight)+"px");
            document.documentElement.style.setProperty('--h2-font-size',(46+calcHeight)+"px");
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
                    // 調理中のリストを更新
                    const cookingList = document.getElementById('cooking-items');
                    cookingList.innerHTML = ''; // クリアする
                    data.cooking.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = item;
                        cookingList.appendChild(li);
                    });

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
        const updateInterval = 500;
        setInterval(updateLists, updateInterval);
        setInterval(changerow,updateInterval);

        // 初回のリスト更新
        updateLists();
        changerow();
    </script>

</body>

</html>
