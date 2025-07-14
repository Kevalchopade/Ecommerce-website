<?php
include('../product/db.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("Location: home.php");
    exit();
}

// Get user ID from session
$username = $_SESSION['username'];

// Get user profile information
$query = "SELECT registered_users.username, registered_users.email, up.full_name, up.phone, up.address, up.gender, up.birthday, up.profile_image 
          FROM registered_users 
          LEFT JOIN user_profile AS up ON registered_users.username = up.username 
          WHERE registered_users.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username); // Changed from "i" to "s" for string
$stmt->execute();   
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user is admin
$isAdmin = false;
$adminQuery = "SELECT role FROM registered_users WHERE username = ?";
$adminStmt = $conn->prepare($adminQuery);
$adminStmt->bind_param("s", $username); // Changed from "i" to "s" for string
$adminStmt->execute();
$adminResult = $adminStmt->get_result();
$adminData = $adminResult->fetch_assoc();
if ($adminData && $adminData['role'] == 'admin') {
    $isAdmin = true;
}

// Handle profile image upload
if (isset($_POST['upload_image']) && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $target_dir = "../assets/uploads/profile/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
    $new_filename = $username . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if file is an actual image
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if($check !== false) {
        // Check file size (5MB max)
        if ($_FILES["profile_image"]["size"] <= 5000000) {
            // Allow certain file formats
            if($file_extension == "jpg" || $file_extension == "png" || $file_extension == "jpeg" || $file_extension == "gif" ) {
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    // Update profile image in database
                    $update_query = "INSERT INTO user_profile (username, profile_image) VALUES (?, ?) 
                                    ON DUPLICATE KEY UPDATE profile_image = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("sss", $username, $new_filename, $new_filename);
                    $update_stmt->execute();
                    
                    // Refresh page to show new image
                    header("Location: profile.php");
                    exit();
                } else {
                    $upload_error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $upload_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            }
        } else {
            $upload_error = "Sorry, your file is too large.";
        }
    } else {
        $upload_error = "File is not an image.";
    }
}

// Handle profile details update
if (isset($_POST['update_details'])) {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    
    // Update profile details in database
    $update_query = "INSERT INTO user_profile (username, full_name, phone, address, gender, birthday) 
                    VALUES (?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE full_name = ?, phone = ?, address = ?, gender = ?, birthday = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssssssss", $username, $full_name, $phone, $address, $gender, $birthday, 
                          $full_name, $phone, $address, $gender, $birthday);
    $update_stmt->execute();
    
    // Refresh page to show updated information
    header("Location: profile.php");
    exit();
}

// Get orders first
$orders_query = "SELECT * FROM orders WHERE username = ? ORDER BY order_date DESC";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("s", $username);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();

// Create a new array for orders with item counts
$orders_with_counts = array();

// Loop through each order and get its total quantity
while ($order = $orders_result->fetch_assoc()) {
    // Query to get the sum of quantities for this order
    $item_query = "SELECT SUM(quantity) as total_quantity FROM order_items WHERE order_id = ?";
    $item_stmt = $conn->prepare($item_query);
    $item_stmt->bind_param("s", $order['id']); // Using "s" because order_id is varchar
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    $item_data = $item_result->fetch_assoc();
    
    // Add total quantity to order data
    $order['item_count'] = $item_data['total_quantity'] ?: 0; // Use 0 if null
    $orders_with_counts[] = $order;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Smart Wear</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <link rel="stylesheet" href="/assets/css/profile.css" />
    <link rel="stylesheet" href="/assets/css/login.css">
    <style>
        /* Modern Profile Card Styles */
        .section-p1 {
            padding: 40px 80px;
        }
        
        .profile-container {
            display: flex;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .profile-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 250px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: none;
            text-align: center;
            position: relative;
            background: linear-gradient(135deg, #f5f7fa 0%, #e6e9f0 100%);
        }
        
        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 20px;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        
        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .profile-image-container:hover .profile-image-overlay {
            opacity: 1;
        }
        
        .camera-icon {
            color: white;
            font-size: 30px;
        }
        
        .profile-info {
            width: 100%;
        }
        
        .profile-info h3 {
            margin: 5px 0;
            font-size: 18px;
            color: #333;
        }
        
        .profile-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        
        .file-upload {
            display: none;
        }
        
        /* Tabs Styling */
        .profile-tabs {
            flex: 1;
            min-width: 0;
        }
        
        .tab-navigation {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab-button {
            padding: 10px 20px;
            border: none;
            background: none;
            font-size: 16px;
            cursor: pointer;
            color: #666;
            position: relative;
        }
        
        .tab-button.active {
            color: #088178;
            font-weight: bold;
        }
        
        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #088178;
        }
        
        .tab-panel {
            display: none;
        }
        
        .tab-panel.active {
            display: block;
        }
        
        /* Details Tab */
        .detail-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-item label {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        
        .detail-item p {
            flex: 1;
            margin: 0;
            color: #333;
        }
        
        .empty {
            color: #aaa;
            font-style: italic;
        }
        
        .edit-button {
            padding: 10px 20px;
            background-color: #088178;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .edit-button:hover {
            background-color: #066e67;
        }
        
        /* Order Cards */
        .order-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            background-color: white;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-id {
            font-weight: bold;
            color: #333;
        }
        
        .order-date {
            color: #666;
        }
        
        .order-info {
            display: flex;
            flex-wrap: wrap;
        }
        
        .order-info-item {
            flex: 1;
            min-width: 120px;
            margin-bottom: 10px;
        }
        
        .order-info-item span {
            display: block;
            font-size: 12px;
            color: #666;
        }
        
        .order-info-item strong {
            display: block;
            font-size: 14px;
            color: #333;
        }
        
        .view-order-button {
            padding: 5px 15px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .view-order-button:hover {
            background-color: #e6e6e6;
        }
        
        /* No Orders State */
        .no-orders {
            text-align: center;
            padding: 40px 20px;
        }
        
        .no-orders p {
            margin-bottom: 20px;
            color: #666;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 90%;
            overflow-y: auto;
            position: relative;
        }
        
        .close-modal {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .cancel-button {
            padding: 8px 15px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .save-button {
            padding: 8px 15px;
            background-color: #088178;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .cancel-button:hover {
            background-color: #e6e6e6;
        }
        
        .save-button:hover {
            background-color: #066e67;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .section-p1 {
                padding: 20px;
            }
            
            .profile-container {
                flex-direction: column;
            }
            
            .profile-card {
                width: 100%;
                margin-bottom: 20px;
            }
            
            .detail-item {
                flex-direction: column;
            }
            
            .detail-item label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
<section id="header">
        <a href="/pages/home.php"><img src="/assets/img/logo.png" alt="" /></a>
  
        <div>
          <ul id="navbar">
              <li class="dropdown">
                  <a href="/product/shop.php?category=men" class="dropdown-btn">Men</a>
                  <ul class="dropdown-menu">
                      <li><a href="/product/shop.php?category=men&subcategory=top-wear">Upperwear</a></li>
                      <li><a href="/product/shop.php?category=men&subcategory=bottom-wear">Lowerwear</a></li>
                  </ul>
              </li>
              <li class="dropdown"> 
                  <a href="/product/shop.php?category=women" class="dropdown-btn">Women</a>
                  <ul class="dropdown-menu">
                      <li><a href="/product/shop.php?category=women&subcategory=top-wear">Upperwear</a></li>
                      <li><a href="/product/shop.php?category=women&subcategory=bottom-wear">Lowerwear</a></li>
                  </ul>
              </li>
              <li class="dropdown">
                  <a href="/product/shop.php?category=accessories" class="dropdown-btn">Accessories</a>
                  <ul class="dropdown-menu">
                      <li><a href="/product/shop.php?category=accessories&subcategory=men">man</a></li>
                      <li><a href="/product/shop.php?category=accessories&subcategory=women">Women</a></li>
                  </ul>
              </li>

              <li id="lg-dash"><i>|</i></li>
              <li id="lg-wishlist"><a href="/pages/wishlist.php"><i class="fa fa-heart"></i></a></li>
              <li id="lg-bag"><a href="/pages/cart.php"><i class="fas fa-shopping-bag"></i></a></li>
  
              <li id="lg-profile" class="profile-dropdown">
    <button id="profile-btn">
        <i class="fas fa-user"></i>
    </button>
    <ul class="dropdown-menu" id="profile-menu">
        <?php
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            echo "<li><a href='/pages/profile.php'>My Profile</a></li>";
            echo "<li><a href='/pages/wishlist.php'>Wishlist</a></li>";
            echo "<li><a href='/pages/orders.php'>My Orders</a></li>";
            echo "<li><a href='/pages/contact.php'>Contact Us</a></li>";
            echo "<li class='user'>
                <span>$_SESSION[username]</span> - <a href='../product/logout.php'>Logout</a>
            </li>";
        } else {
            echo "<li class='sign-in'>
                <a href='#' onclick=\"popup('login-popup')\">Log in</a>
            </li>";
            echo "<li><a href='#' onclick=\"openRegisterPopup()\">Register</a></li>";
            echo "<li><a href='#' onclick=\"popup('login-popup')\">Wishlist</a></li>";
            echo "<li><a href='#' onclick=\"popup('login-popup')\">My Orders</a></li>";
            echo "<li><a href='contact.php'>Contact Us</a></li>";
        }
        ?>
    </ul>
</li>
          </ul>
      </div>
        
        <div id="mobile">
            <a href="/pages/cart.html"><i class="fas fa-shopping-bag"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
            <li id="lg-profile">
                <a href="/pages/profile.html"><i class="fas fa-user"></i></a>
            </li>
        </div>
    </section>

    <section class="section-p1">
        <h2>My Profile</h2>
        <div class="profile-container">
            <!-- Profile Card (Left Side) -->
            <div class="profile-card">
                <!-- Profile Image -->
                <div class="profile-image-container" onclick="document.getElementById('profileImageUpload').click()">
                    <?php if(!empty($user['profile_image'])): ?>
                        <img src="../assets/uploads/profile/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-image">
                    <?php else: ?>
                        <i class="fas fa-user" style="font-size: 70px; color: #ccc;"></i>
                    <?php endif; ?>
                    <div class="profile-image-overlay">
                        <i class="fas fa-camera camera-icon"></i>
                    </div>
                </div>
                <form id="imageUploadForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_image" id="profileImageUpload" class="file-upload" accept="image/*">
                    <input type="hidden" name="upload_image" value="1">
                </form>
                
                <?php if(isset($upload_error)): ?>
                    <p style="color: red;"><?php echo $upload_error; ?></p>
                <?php endif; ?>
                
                <!-- Profile Info -->
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
            
            <!-- Profile Tabs (Right Side) -->
            <div class="profile-tabs">
                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    <button class="tab-button active" data-tab="details">Details</button>
                    <button class="tab-button" data-tab="orders">Order History</button>
                    <?php if($isAdmin): ?>
                        <button class="tab-button" data-tab="dashboard">Dashboard</button>
                    <?php endif; ?>
                </div>
                
                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Details Tab -->
                    <div id="details" class="tab-panel active">
                        <?php if(!empty($user['full_name']) || !empty($user['phone']) || !empty($user['address']) || !empty($user['gender']) || !empty($user['birthday'])): ?>
                            <div class="detail-item">
                                <label>Full Name</label>
                                <p><?php echo !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : '<span class="empty">Not specified</span>'; ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Phone Number</label>
                                <p><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : '<span class="empty">Not specified</span>'; ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Address</label>
                                <p><?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : '<span class="empty">Not specified</span>'; ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Gender</label>
                                <p><?php echo !empty($user['gender']) ? htmlspecialchars($user['gender']) : '<span class="empty">Not specified</span>'; ?></p>
                            </div>
                            <div class="detail-item">
                                <label>Birthday</label>
                                <p><?php echo !empty($user['birthday']) ? htmlspecialchars($user['birthday']) : '<span class="empty">Not specified</span>'; ?></p>
                            </div>
                        <?php else: ?>
                            <p class="no-profile-text">Your profile details are not yet complete. Click the button below to add your information.</p>
                        <?php endif; ?>
                        
                        <button class="edit-button" onclick="openModal('editDetailsModal')">Edit Details</button>
                    </div>
                    
                    <!-- Order History Tab -->
                    <div id="orders" class="tab-panel">
                    <?php if(count($orders_with_counts) > 0): ?>
    <?php foreach($orders_with_counts as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <span class="order-id">Order #<?php echo $order['id']; ?></span>
                <span class="order-date">
                    <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                </span>
            </div>
            <div class="order-info">
                <div class="order-info-item">
                    <span>Status</span>
                    <strong><?php echo ucfirst($order['status']); ?></strong>
                </div>
               
                <div class="order-info-item">
                    <span>Total</span>
                    <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                </div>
                <div class="order-info-item" style="text-align: right;">
                    <a href="orders.php?id=<?php echo $order['id']; ?>">
                        <button class="view-order-button">View Details</button>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="no-orders">
        <p>You haven't placed any orders yet.</p>
        <a href="/product/shop.php">
            <button class="edit-button">Start Shopping</button>
        </a>
    </div>
<?php endif; ?>
                    </div>
                    
                    <!-- Dashboard Tab (Admin Only) -->
                    <?php if($isAdmin): ?>
                        <div id="dashboard" class="tab-panel">
                            <p>Admin dashboard access is available via a separate page for better functionality.</p>
                            <a href="dashboard.php">
                                <button class="edit-button">Go to Admin Dashboard</button>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Details Modal -->
    <div id="editDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('editDetailsModal')">&times;</span>
            <h2>Edit Profile Details</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo (isset($user['gender']) && $user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($user['gender']) && $user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo (isset($user['gender']) && $user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        <option value="Prefer not to say" <?php echo (isset($user['gender']) && $user['gender'] == 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($user['birthday'] ?? ''); ?>">
                </div>
                <div class="form-buttons">
                    <button type="button" class="cancel-button" onclick="closeModal('editDetailsModal')">Cancel</button>
                    <button type="submit" name="update_details" class="save-button">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="section-p1">
        <div class="col">
          <h4>Get in Touch</h4>
          <p>
            <strong>Email: </strong>smartwearservice2025@gmail.com
          </p>
          <p><strong>Phone: </strong>+91 9321620322 /+91 7248930066</p>
          </div>
        </div>

    <div class="col">
      <h4>About</h4>
      <a href="/pages/about.php">About Us</a>
      <a href="/pages/delivery-info.php">Delivery Information</a>
      <a href="/pages/privacy.php">Privacy Policy</a>
      <a href="/pages/terms.php">Terms & Conditions</a>
      <a href="/pages/contact.php">Contact Us</a>
    </div>

    <div class="col">
      <h4>My Account</h4>
      <a href="#" onclick="popup('login-popup'); return false;">Sign In</a>
      <a href="/pages/cart.php">View Cart</a>
      <a href="/pages/wishlist.php">My Wishlist</a>
      <a href="/pages/orders.php">Track My Order</a>
      <a href="/pages/contact.php">Help</a>
    </div>
    
    <div class="col">
      <h4>Quick Links</h4>
      <a href="/pages/home.php">Home</a>
      <a href="/product/shop.php">Shop</a>
      <a href="/pages/contact.php">Contact</a>
      <a href="#" id="openSizeGuide">Size Guide</a>
    </div>

    <div class="copyright">
          <p>Ⓒ 2025,SmartWear.com- Online Shopping App</p>
    </div>

      </footer>

    <div class="popup-container" id="login-popup">
        <div class="popup">
          <form method="POST" action="../product/login_register.php">
            <h2>
                <span class="login-heading">USER LOGIN</span>
                <span class="register-close-btn" onclick="popup('login-popup')">&times;</span>
            </h2>        
            <input type="text" placeholder="E-mail or Username" name="email_username">
            <input type="password" placeholder="Password" name="password">
            <button type="submit" class="login-btn" name="login">LOGIN</button>
            <div class="login-links">
    <a href="#" class="forgot-password" onclick="openForgotPasswordPopup(); return false;">Forgot Password</a>
    <a href="#" class="create-account" onclick="openRegisterPopup(); return false;">Create Account</a>
</div>        
          </form>
        </div>
    </div>
    
    <!-- Registration Popup -->
    <div id="registerPopup" class="register-popup">
        <div class="register-popup-content">
            <span class="register-close-btn" onclick="closeRegisterPopup()">&times;</span>
            <h2>USER REGISTER</h2>
            <form method="POST" action="../product/login_register.php">
                <input type="text" placeholder="Full Name" name="fullname" required>
                <input type="text" placeholder="Username" name="username" required>
                <input type="email" placeholder="E-mail" name="email" required>
                <input type="password" placeholder="Password" name="password" required>
                <button type="submit" name="register">REGISTER</button>
            </form>
        </div>
    </div>

    <div id="forgotPasswordContainer" class="popup-container">
    <div class="popup">
        <span class="register-close-btn" onclick="closeForgotPasswordPopup()">&times;</span>
        <h2 class="forget-password-headline">RESET PASSWORD</h2>
        <p>Enter your email address to verify your account and reset your password.</p>
        <form id="forgotPasswordForm" method="POST" action="../product/verify_email.php">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="verify-email-submit" class="forget-password-button">Verify</button>
        </form>
        <div id="emailVerificationMessage"></div>
    </div>
</div>

<!-- Reset Password Popup -->
<div id="resetPasswordPopup" class="popup-container">
    <div class="popup">
        <span class="register-close-btn" onclick="closeResetPasswordPopup()">&times;</span>
        <h2 class="reset-password-headline">SET NEW PASSWORD</h2>
        <form id="resetPasswordForm">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit" class="reset-password-button">Reset Password</button>
        </form>
        <div class="reset-message"></div>
    </div>
</div>

<div id="sizeGuidePopup" class="popup-container">
    <div class="popup size-guide-popup">
        <span class="register-close-btn" onclick="closeSizeGuidePopup()">&times;</span>
        <h2>SIZE GUIDE</h2>
        <div class="size-guide-content">
            <table class="size-chart">
                <thead>
                    <tr>
                        <th>Size</th>
                        <th>Chest (in)</th>
                        <th>Waist (in)</th>
                        <th>Hips (in)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>S</td>
                        <td>33-35</td>
                        <td>26-28</td>
                        <td>36-38</td>
                    </tr>
                    <tr>
                        <td>M</td>
                        <td>35-37</td>
                        <td>28-30</td>
                        <td>38-40</td>
                    </tr>
                    <tr>
                        <td>L</td>
                        <td>37-39</td>
                        <td>30-32</td>
                        <td>40-42</td>
                    </tr>
                    <tr>
                        <td>XL</td>
                        <td>39-41</td>
                        <td>32-34</td>
                        <td>42-44</td>
                    </tr>
                    <tr>
                        <td>XXL</td>
                        <td>41-43</td>
                        <td>34-36</td>
                        <td>44-46</td>
                    </tr>
                </tbody>
            </table>
            <p class="size-guide-note">Measurements are body measurements, not garment measurements.</p>
        </div>
    </div>
</div>

<script src="/assets/js/size_guide.js"></script>

    <script>
        // Profile Image Upload Handling
        document.getElementById('profileImageUpload').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                document.getElementById('imageUploadForm').submit();
            }
        });
        
        // Tab Navigation
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanels = document.querySelectorAll('.tab-panel');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and panels
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanels.forEach(panel => panel.classList.remove('active'));
                
                // Add active class to clicked button and corresponding panel
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });
    </script>
    <script src="/assets/js/script.js"></script>
</body>
</html>