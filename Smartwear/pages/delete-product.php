<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../pages/home.php");
    exit;
}

include('../product/db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM products WHERE prod_id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: product-management.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>
