<?php
include "db.php";
session_start();

// Set up response array
$response = array();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $response['success'] = false;
    $response['error'] = 'login_required';
    echo json_encode($response);
    exit();
}

// Check if product_id was sent
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    $response['success'] = false;
    $response['error'] = 'missing_product';
    echo json_encode($response);
    exit();
}

$username = $_SESSION['username'];
$product_id = intval($_POST['product_id']);

// First check if this product already exists in the user's wishlist
$check_query = "SELECT * FROM wishlist WHERE username = ? AND prod_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("si", $username, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Product already in wishlist
    $response['success'] = true;
    $response['message'] = 'Product already in wishlist!';
} else {
    // Add new product to wishlist
    $insert_query = "INSERT INTO wishlist (username, prod_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("si", $username, $product_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Product added to wishlist!';
    } else {
        $response['success'] = false;
        $response['error'] = 'db_error';
        $response['message'] = 'Failed to add product to wishlist.';
    }
}

// Return JSON response
echo json_encode($response);
?>