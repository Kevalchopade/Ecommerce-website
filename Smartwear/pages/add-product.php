<?php
include('../product/db.php');

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $size = $_POST['size'];

    // File upload
    $img1 = $_FILES['image1']['name'];
    $tmp1 = $_FILES['image1']['tmp_name'];
    move_uploaded_file($tmp1, "../assets/uploads/$img1");

    $img2 = $_FILES['image2']['name'];
    $tmp2 = $_FILES['image2']['tmp_name'];
    if ($img2 != "") {
        move_uploaded_file($tmp2, "../assets/uploads/$img2");
    }

    $sql = "INSERT INTO products (name, price, category, subcategory, brand, image1, image2, description, size)
            VALUES ('$name', '$price', '$category', '$subcategory', '$brand', '$img1', '$img2', '$description', '$size')";
    mysqli_query($conn, $sql);
    header("Location: product-management.php");
}
?>
