<?php
include('../product/db.php');
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true ) {
    header("Location: ../pages/home.php");
    exit();
}

// Function to upload image and return filename
function uploadImage($file, $target_dir = "../product/images/") {
    if ($file['error'] == 0) {
        $filename = time() . '_' . basename($file['name']);
        $target_file = $target_dir . $filename;
        
        // Check if file is an actual image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return false;
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $filename;
        }
    }
    return false;
}

// Add new product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';
    
    // Upload main image
    $image1 = uploadImage($_FILES['image1']);
    if (!$image1) {
        $_SESSION['error_message'] = "Error uploading main image.";
        header("Location: product-management.php");
        exit();
    }
    
    // Upload secondary image (optional)
    $image2 = isset($_FILES['image2']) && $_FILES['image2']['error'] == 0 ? uploadImage($_FILES['image2']) : null;
    
    // Insert product into database
    $query = "INSERT INTO products (name, price, category_id, subcategory_id, brand, description, image, image2, size) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdiiissss", $name, $price, $category_id, $subcategory_id, $brand, $description, $image1, $image2, $sizes);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding product: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: product-management.php");
    exit();
}

// Update existing product
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';
    
    // Get current product data
    $get_product = "SELECT image, image2 FROM products WHERE id = ?";
    $stmt = $conn->prepare($get_product);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    // Handle main image upload (if provided)
    if ($_FILES['image1']['error'] == 0) {
        $image1 = uploadImage($_FILES['image1']);
        if (!$image1) {
            $_SESSION['error_message'] = "Error uploading main image.";
            header("Location: product-management.php");
            exit();
        }
    } else {
        $image1 = $product['image']; // Keep existing image
    }
    
    // Handle secondary image upload (if provided)
    if ($_FILES['image2']['error'] == 0) {
        $image2 = uploadImage($_FILES['image2']);
        if (!$image2) {
            $_SESSION['error_message'] = "Error uploading secondary image.";
            header("Location: product-management.php");
            exit();
        }
    } else {
        $image2 = $product['image2']; // Keep existing image
    }
    
    // Update product in database
    $query = "UPDATE products 
              SET name = ?, price = ?, category_id = ?, subcategory_id = ?, 
                  brand = ?, description = ?, image = ?, image2 = ?, size = ? 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdiiissssi", $name, $price, $category_id, $subcategory_id, 
                      $brand, $description, $image1, $image2, $sizes, $product_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating product: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: product-management.php");
    exit();
}

// If no valid action is provided, redirect back
header("Location: product-management.php");
exit();
?>