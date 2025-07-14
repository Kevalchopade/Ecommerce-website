<?php
session_start();
include('../product/db.php');

// Check admin access
if (!isset($_SESSION['logged_in'])) {
    header("Location: /pages/home.php");
    exit();
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from form
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    // Validate input data
    if (empty($order_id) || empty($new_status)) {
        header("Location: order-management.php?update=error&message=" . urlencode("Order ID or status cannot be empty"));
        exit();
    }
    
    // Validate status value
    $valid_statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        header("Location: order-management.php?update=error&message=" . urlencode("Invalid status value"));
        exit();
    }
    
    // Using prepared statement to safely handle the order_id as a string
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ss", $new_status, $order_id);  // Using 'ss' for two strings
        
        if ($stmt->execute()) {
            // Success - redirect with success message
            header("Location: order-management.php?update=success&order=" . urlencode($order_id));
            exit();
        } else {
            // Error with execution
            header("Location: order-management.php?update=error&message=" . urlencode("Database error: " . $stmt->error));
            exit();
        }
    } else {
        // Error with prepared statement
        header("Location: order-management.php?update=error&message=" . urlencode("Database error: " . $conn->error));
        exit();
    }
} else {
    // Not a POST request - redirect to main page
    header("Location: order-management.php");
    exit();
}
?>