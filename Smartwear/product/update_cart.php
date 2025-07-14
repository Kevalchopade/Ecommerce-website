<?php
session_start();
include "db.php";
header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['error' => 'login_required']);
    exit;
}

// Get parameters
if(isset($_POST['product_id']) && isset($_POST['action'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    $user_id = $_SESSION['user_id'];
    
    switch($action) {
        case 'increase':
            $query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = '$user_id' AND product_id = '$product_id'";
            break;
        case 'decrease':
            $query = "UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE user_id = '$user_id' AND product_id = '$product_id'";
            break;
        case 'remove':
            $query = "DELETE FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
            break;
        default:
            echo json_encode(['error' => 'invalid_action']);
            exit;
    }
    
    $result = mysqli_query($conn, $query);
    
    if($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'query_failed']);
    }
} else {
    echo json_encode(['error' => 'missing_parameters']);
}
?>