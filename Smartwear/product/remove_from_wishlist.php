<?php
session_start();
include "db.php";
header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['error' => 'login_required']);
    exit;
}

// Get product ID
if(isset($_POST['product_id'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $user_id = $_SESSION['user_id'];
    
    $query = "DELETE FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $result = mysqli_query($conn, $query);
    
    if($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'delete_failed']);
    }
} else {
    echo json_encode(['error' => 'missing_product_id']);
}
?>