<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUser = $_POST['username'] ?? '';
    $inputPass = $_POST['password'] ?? '';

    $hashedData = json_decode(file_get_contents('/hashed.json'), true);
    $storedUser = $hashedData['username'] ?? '';
    $storedHash = $hashedData['password'] ?? '';

    if ($inputUser === $storedUser && password_verify($inputPass, $storedHash)) {
        // セッションを作成
        session_regenerate_id(true);
        $_SESSION['authenticated'] = true;

        // 元のリクエストページへリダイレクト
        $redirect = $_SESSION['redirect_to'] ?? 'index.php';
        unset($_SESSION['redirect_to']);
        header("Location: $redirect");
        exit;
    } else {
        $error = "ユーザー名またはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>