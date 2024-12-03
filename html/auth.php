<?php
session_start();

$loginEnabled = getenv('LOGIN_ENABLED') === 'true';

if ($loginEnabled && (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true)) {
    header('Location: login.php');
    exit;
}

?>