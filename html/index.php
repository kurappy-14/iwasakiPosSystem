<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>

<!-- ここに各種画面への導線を用意 -->

<!DOCTYPE html>
<html>
<head>
    <title>中継</title>
    <meta charset="utf-8">
    <style>
        h1 {
            text-align: center;
        }
        
        .menu {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        a{
            margin: 10px;
            padding: 10px;
            border: 1px solid black;
            border-radius: 5px;
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
    <h1>中継</h1>
    <nav id="top-menu" class="menu">
        <a href="adminUI/admin.php">管理者画面</a>
        <a href="announcementPanel/index.html">呼び出しパネル（大）</a>
        <a href="announcementPanel/division1.html">呼び出しパネル（小1）</a>
        <a href="announcementPanel/division2.html">呼び出しパネル（小2）</a>
        <a href="kitchenUI/kitchen.html">キッチン画面</a>
        <a href="register/register.html">レジ画面</a>
    </nav>
</html>