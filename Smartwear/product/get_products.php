<?php
include "db.php";

header('Content-Type: application/json');

// Initialize query
$query = "SELECT * FROM products WHERE 1=1";

// Apply category filter
if(isset($_GET['category']) && !empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $query .= " AND category = '$category'";
}

// Apply subcategory filter
if(isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
    $subcategory = mysqli_real_escape_string($conn, $_GET['subcategory']);
    $query .= " AND subcategory = '$subcategory'";
}

// Apply search filter
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%' OR category LIKE '%$search%' OR subcategory LIKE '%$search%')";
}

// Execute query
$result = mysqli_query($conn, $query);

if(!$result) {
    echo json_encode(array('error' => 'Database error: ' . mysqli_error($conn)));
    exit;
}

$products = array();

while($row = mysqli_fetch_assoc($result)) {
    // Make sure the prod_id is properly included
    // This ensures that the field name matches what your product.php is expecting
    if (isset($row['id']) && !isset($row['prod_id'])) {
        $row['prod_id'] = $row['id']; // Map id to prod_id if needed
    }
    $products[] = $row;
}

echo json_encode($products);