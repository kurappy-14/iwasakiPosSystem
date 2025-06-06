<?php
$AUTH_FILE_PATH = getenv('AUTH_FILE_PATH');
require $AUTH_FILE_PATH;
?>

<!-- ここに各種画面への導線を用意 -->

<!DOCTYPE html>
<html>
<head>
    <title>HOME</title>
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
            text-align: center;
            width: 16em;
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
    <h1>HOME</h1>
    <nav id="top-menu" class="menu">
        <a href="adminUI/admin.php">管理者画面</a>
        <a href="announcementPanel/full.php">呼び出しパネル</a>
        <a href="announcementPanel/division1.php">呼び出しパネル（調理中）</a>
        <a href="announcementPanel/division2.php">呼び出しパネル（調理完了）</a>
        <a href="kitchenUI/kitchen_mng.php">キッチン画面</a>
        <a href="register/register.php">レジ画面</a>
    </nav>
</html>