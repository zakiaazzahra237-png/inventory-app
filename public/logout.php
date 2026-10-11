<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: /inventory-app/public/login.php');
exit;