<?php
$id = $_GET["id"] ?? 0;
header("Location: index.php?p=reports/yas/report-new-yas&id=" . $id);
exit;
?>
