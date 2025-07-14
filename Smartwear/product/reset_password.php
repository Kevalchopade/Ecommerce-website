<?php
include('../product/db.php') ;
session_start();

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// Check if user is trying to reset password
if (isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    // Check if the reset email is stored in session
    if (!isset($_SESSION['reset_email'])) {
        $response['message'] = 'Email verification required before resetting password!';
        echo json_encode($response);
        exit();
    }
    
    $email = $_SESSION['reset_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords match
    if ($new_password !== $confirm_password) {
        $response['message'] = 'Passwords do not match!';
        echo json_encode($response);
        exit();
    }
    
    // Password length validation
    if (strlen($new_password) < 6) {
        $response['message'] = 'Password must be at least 6 characters long.';
        echo json_encode($response);
        exit();
    }
    
    // Hash the password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password in registered_users table
    $query = "UPDATE registered_users SET password = '$hashed_password' WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_affected_rows($conn) > 0) {
        // Password updated successfully in registered_users table
        unset($_SESSION['reset_email']); // Clear the session variable
        $response['success'] = true;
        $response['message'] = 'Password has been reset successfully!';
    } else {
        // Try users table if necessary
        $query2 = "UPDATE registered_users SET password = '$hashed_password' WHERE email = '$email'";
        $result2 = mysqli_query($conn, $query2);
        
        if ($result2 && mysqli_affected_rows($conn) > 0) {
            // Password updated successfully in users table
            unset($_SESSION['reset_email']);
            $response['success'] = true;
            $response['message'] = 'Password has been reset successfully!';
        } else {
            // Failed to update password in either table
            $response['message'] = 'Failed to update password. Please try again.';
        }
    }
} else {
    $response['message'] = 'Invalid request!';
}

echo json_encode($response);
?>