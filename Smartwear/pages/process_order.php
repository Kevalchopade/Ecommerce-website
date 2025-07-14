<?php
include "../product/db.php";
session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    $_SESSION['checkout_error'] = "Please login to place an order";
    header("Location: index.php");
    exit();
}

// Check if form was submitted
if($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: checkout.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['username'];

// Get form data
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$first_name = mysqli_real_escape_string($conn, $_POST['firstName']);
$last_name = mysqli_real_escape_string($conn, $_POST['lastName']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$apartment = mysqli_real_escape_string($conn, $_POST['apartment'] ?? '');
$city = mysqli_real_escape_string($conn, $_POST['city']);
$state = mysqli_real_escape_string($conn, $_POST['state']);
$zipcode = mysqli_real_escape_string($conn, $_POST['zipcode']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']); // Changed from payment-method to payment_method

// Get order summary data
$subtotal = floatval($_POST['subtotal']);
$tax = floatval($_POST['tax']);
$shipping = floatval($_POST['shipping']);
$grand_total = floatval($_POST['grand_total']);

// Get cart data
$cart_data = json_decode($_POST['cart_data'], true);
if(!$cart_data) {
    $_SESSION['checkout_error'] = "Invalid cart data";
    header("Location: checkout.php");
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Create order record
    $order_id = uniqid('ORD');
    $order_date = date('Y-m-d H:i:s');
    $shipping_address = "$address, $apartment, $city, $state - $zipcode";
    $status = "Pending";
    
    // Insert into orders table
    $order_stmt = $conn->prepare("INSERT INTO orders (order_id, username, order_date, shipping_address, 
                                payment_method, subtotal, tax, shipping_fee, total_amount, status, 
                                first_name, last_name, email, phone) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $order_stmt->bind_param("sssssddddsssss", 
                        $order_id, 
                        $user_id, 
                        $order_date, 
                        $shipping_address, 
                        $payment_method, 
                        $subtotal, 
                        $tax, 
                        $shipping, 
                        $grand_total, 
                        $status,
                        $first_name,
                        $last_name,
                        $email,
                        $phone);
    
    $order_stmt->execute();
    
    // Insert order items
    $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, prod_id, quantity, size, price) 
                                      VALUES (?, ?, ?, ?, ?)");
    
    foreach($cart_data as $item) {
        $prod_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $size = isset($item['size']) ? $item['size'] : null;
        
        $order_item_stmt->bind_param("sissd", $order_id, $prod_id, $quantity, $size, $price);
        $order_item_stmt->execute();
    }
    
    // If coming from cart page, clear the cart
    if(!isset($_GET['prod_id'])) {
        $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE username = ?");
        $clear_cart_stmt->bind_param("s", $user_id);
        $clear_cart_stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Set success message and redirect to orders page
    $_SESSION['order_success'] = "Your order has been placed successfully! Order ID: $order_id";
    header("Location: orders.php");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    $_SESSION['checkout_error'] = "Failed to place order: " . $e->getMessage();
    header("Location: checkout.php");
    exit();
}
?>