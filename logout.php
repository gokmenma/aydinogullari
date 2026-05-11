<?php 
include("configs/index.php");



session_destroy();
header("Location: login.php");
exit;
?>