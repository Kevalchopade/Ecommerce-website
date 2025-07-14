<?php
include "../product/db.php";
session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    $_SESSION['login_required'] = "Please login to perform this action";
    header("Location: index.php");
    exit();
}

// Check if order_id is provided
if(!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    $_SESSION['order_error'] = "Invalid order ID";
    header("Location: orders.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
$username = $_SESSION['username'];

// Verify that the order belongs to the current user and is in a cancellable state
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND username = ?");
$stmt->bind_param("ss", $order_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    $_SESSION['order_error'] = "Order not found or you don't have permission to cancel it";
    header("Location: orders.php");
    exit();
}

$order = $result->fetch_assoc();

// Check if the order is in a state that can be cancelled
$cancellable_statuses = ['Pending', 'Processing'];
if(!in_array($order['status'], $cancellable_statuses)) {
    $_SESSION['order_error'] = "This order cannot be cancelled because it is already {$order['status']}";
    header("Location: orders.php");
    exit();
}

// Update order status to Cancelled
$stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled', updated_at = NOW() WHERE order_id = ?");
$stmt->bind_param("s", $order_id);

if($stmt->execute()) {
    $_SESSION['order_success'] = "Order #{$order_id} has been cancelled successfully";
} else {
    $_SESSION['order_error'] = "Failed to cancel order. Please try again.";
}

// Redirect back to orders page
header("Location: orders.php");
exit();
?>