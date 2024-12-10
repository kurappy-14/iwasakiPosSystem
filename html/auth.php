<?php
session_start();

$loginEnabled = getenv('LOGIN_ENABLED') === 'true';

if ($loginEnabled && (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true)) {
    $hostURL = 'http://' . $_SERVER['HTTP_HOST'];
    header('Location: ' . $hostURL . '/login.php');
    exit;
}
?>