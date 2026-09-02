<?php
session_start();
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM flowers WHERE id = $id");
}

header("Location: ../admin/flowers.php");
exit();
?>