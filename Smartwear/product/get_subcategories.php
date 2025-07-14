<?php
// Include database connection
include "db.php";

// Get the selected category
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

// Initialize response array
$subcategories = [];

if (!empty($category)) {
    // Query to get subcategories for the selected category
    // Check both 'subcategory' and 'sub-category' field names
    $query = "SELECT DISTINCT subcategory FROM products WHERE category = '$category' ORDER BY subcategory";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        // If the first query fails, try with 'sub-category' field name
        $query = "SELECT DISTINCT `sub-category` AS subcategory FROM products WHERE category = '$category' ORDER BY `sub-category`";
        $result = mysqli_query($conn, $query);
    }
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($row['subcategory']) && !empty($row['subcategory'])) {
                $subcategories[] = $row['subcategory'];
            }
        }
    }
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($subcategories);
?>