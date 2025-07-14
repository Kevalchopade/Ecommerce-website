<?php
include "db.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart']);
    exit();
}

// Check if product ID and quantity are set
if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$username = $_SESSION['username'];
$product_id = intval($_POST['product_id']);
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default to 1 if not set
$size = isset($_POST['size']) && !empty($_POST['size']) ? $_POST['size'] : "M"; // Default to "M" if not set

// Check if product exists in database
$stmt = $conn->prepare("SELECT * FROM products WHERE prod_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

// Check if product is already in cart with the same size
$stmt = $conn->prepare("SELECT * FROM cart WHERE username = ? AND prod_id = ? AND size = ?");
$stmt->bind_param("sis", $username, $product_id, $size);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity if product already in cart
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_quantity, $row['id']);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
} else {
    // Add new product to cart
    $stmt = $conn->prepare("INSERT INTO cart (username, prod_id, quantity, size) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $username, $product_id, $quantity, $size);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
    }
}

$conn->close();
?>