<?php
include('../product/db.php');
session_start();

if (!isset($_SESSION['logged_in']) ) {
    header("Location: ../pages/home.php");
    exit();
}

if (isset($_GET['id'])) {
    $prod_id = $_GET['id'];
    $query = "SELECT * FROM products WHERE prod_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
} else {
    header("Location: product-management.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $size = $_POST['size'];

    $updateQuery = "UPDATE products SET name=?, price=?, category=?, subcategory=?, brand=?, description=?, size=? WHERE prod_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sdsssssi", $name, $price, $category, $subcategory, $brand, $description, $size, $prod_id);
    $stmt->execute();

    header("Location: product-management.php");
    exit();
}
?>

<!-- HTML FORM FOR EDITING -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <h2>Edit Product</h2>
    <form method="POST">
        <label>Name: <input type="text" name="name" value="<?= $product['name'] ?>" required></label><br>
        <label>Price: <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required></label><br>
        <label>Category: <input type="text" name="category" value="<?= $product['category'] ?>" required></label><br>
        <label>Sub-Category: <input type="text" name="subcategory" value="<?= $product['subcategory'] ?>" required></label><br>
        <label>Brand: <input type="text" name="brand" value="<?= $product['Brand'] ?>" required></label><br>
        <label>Description: <textarea name="description" required><?= $product['description'] ?></textarea></label><br>
        <label>Size:
            <select name="size" required>
                <?php foreach (['S', 'M', 'L', 'XL', 'XXL'] as $sz): ?>
                    <option value="<?= $sz ?>" <?= $product['size'] == $sz ? 'selected' : '' ?>><?= $sz ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Update Product</button>
    </form>
</body>
</html>
