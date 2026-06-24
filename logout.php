<?php 
require_once 'bootstrap.php';

if (isset($_SESSION['lid'])) {
    log_info("Sistemden çıkış yaptı", "database");
}

session_destroy();
header("Location: login.php");
exit;
?>