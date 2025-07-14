<?php
include('../product/db.php');
session_start();

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// For debugging
error_log("Email verification request received");

if (isset($_POST['email']) && !empty($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    error_log("Checking email: " . $email);
    
    // Check if email exists in the database - check both tables if needed
    $query = "SELECT * FROM registered_users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Email exists in registered_users table
        error_log("Email found in registered_users table");
        $_SESSION['reset_email'] = $email;
        $response['success'] = true;
        $response['message'] = 'Email verified successfully!';  
    } else {
        // Try users table if you have multiple tables
        error_log("Email not found in registered_users table, checking users table");
        $query2 = "SELECT * FROM users WHERE email = '$email'";
        $result2 = mysqli_query($conn, $query2);
        
        if ($result2 && mysqli_num_rows($result2) > 0) {
            // Email exists in users table
            error_log("Email found in users table");
            $_SESSION['reset_email'] = $email;
            $response['success'] = true;
            $response['message'] = 'Email verified successfully!';
        } else {
            // Email doesn't exist in either table
            error_log("Email not found in any table");
            $response['message'] = 'Email not found in our records!';
        }
    }
} else {
    error_log("No email provided");
    $response['message'] = 'Please provide an email address.';
}

error_log("Sending response: " . json_encode($response));
echo json_encode($response);
?>