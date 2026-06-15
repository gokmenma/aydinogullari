<?php
require_once '../../configs/config.php';

$id = $_POST["id"];
    $sql = $ac->prepare("SELECT * FROM offertemplate where id = ?");
    $sql->execute(array($id)); // Sorguyu çalıştır

    $row = $sql->fetch(PDO::FETCH_ASSOC);
    echo json_encode(array("content" => $row["Content"]));
