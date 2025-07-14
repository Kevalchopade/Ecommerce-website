<?php
include('../product/db.php');
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true ) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Get product ID from request
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid product ID']);
    exit();
}

// Fetch product details
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Product not found']);
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($product);
?>