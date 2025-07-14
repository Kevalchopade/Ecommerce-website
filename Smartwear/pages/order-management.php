<?php
session_start();
include('../product/db.php');

// Check admin access
if (!isset($_SESSION['logged_in'])) {
    header("Location: /pages/home.php");
    exit();
}

// Check for update success/error messages
$update_message = '';
if (isset($_GET['update'])) {
    if ($_GET['update'] == 'success') {
        $update_message = "<div class='alert alert-success'>Order #" . htmlspecialchars($_GET['order']) . " status updated successfully!</div>";
    } else if ($_GET['update'] == 'error') {
        $update_message = "<div class='alert alert-error'>Error: " . htmlspecialchars($_GET['message']) . "</div>";
    }
}

// Fetch orders
$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = mysqli_query($conn, $sql);

// If there was an error with the main query, capture it
$query_error = '';
if (!$result) {
    $query_error = "Error fetching orders: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link rel="stylesheet" href="/assets/css/login.css">
    <style>
        /* Notification styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-close {
            position: absolute;
            right: 15px;
            top: 15px;
            cursor: pointer;
            font-weight: bold;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Order status styles */
        .status-pending {
            color: #856404;
            background-color: #fff3cd;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: 500;
        }
        
        .status-shipped {
            color: #0c5460;
            background-color: #d1ecf1;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: 500;
        }
        
        .status-delivered {
            color: #155724;
            background-color: #d4edda;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: 500;
        }
        
        .status-cancelled {
            color: #721c24;
            background-color: #f8d7da;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: 500;
        }
        
        .update-btn {
            padding: 4px 8px;
            background-color: #088178;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .update-btn:hover {
            background-color: #066e67;
        }
    </style>
</head>
<body>
    <!-- Main Navigation Bar -->
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

    <!-- Admin Navigation Bar -->
    <section id="admin-navbar">
        <div class="admin-nav-container">
            <div class="admin-nav-left">
                <h2>Admin Panel</h2>
            </div>
            <ul class="admin-nav-tabs">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="product-management.php">Product Management</a></li>
                <li><a href="order-management.php" class="active">Order Management</a></li>
                <li><a href="customer-management.php">Customer Management</a></li>
            </ul>
            <div class="admin-nav-right">
                <a href="/pages/profile.php" class="exit-btn">Exit</a>
            </div>
        </div>
    </section>
    
    <section class="admin-container">
        <h2 style="text-align: center; margin-top: 20px; color: #333;">Order Management</h2>
        
        <!-- Notification area -->
        <div id="notification-area">
            <?php 
            echo $update_message;
            
            if (!empty($query_error)) {
                echo "<div class='alert alert-error'>$query_error <span class='alert-close' onclick='this.parentElement.style.display=\"none\";'>×</span></div>";
            }
            ?>
        </div>
        
        <div class="product-table-container" style="padding: 20px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) { 
                            // Get status class
                            $status_class = 'status-' . strtolower($row['status']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                        <td>₹<?php echo htmlspecialchars($row['total_amount']); ?></td>
                        <td><?php echo date("d M Y", strtotime($row['order_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                        <td>
                            <form method="post" action="update-order-status.php" class="status-form">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                <select name="status">
                                    <option value="Pending" <?php if($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Shipped" <?php if($row['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Delivered" <?php if($row['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="Cancelled" <?php if($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="update-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='9'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="section-p1">
        <div class="col">
            <h4>Get in Touch</h4>
            <p><strong>Email: </strong>smartwearservice2025@gmail.com</p>
            <p><strong>Phone: </strong>+91 9321620322 /+91 7248930066</p>
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

    <!-- All the popups remain the same -->
    <div class="popup-container" id="login-popup">
        <!-- Login popup content unchanged -->
    </div>
    
    <!-- Registration Popup -->
    <div id="registerPopup" class="register-popup">
        <!-- Register popup content unchanged -->
    </div>

    <div id="forgotPasswordContainer" class="popup-container">
        <!-- Forgot password popup content unchanged -->
    </div>

    <div id="resetPasswordPopup" class="popup-container">
        <!-- Reset password popup content unchanged -->
    </div>

    <div id="sizeGuidePopup" class="popup-container">
        <!-- Size guide popup content unchanged -->
    </div>

    <script src="/assets/js/size_guide.js"></script>
    <script src="/assets/js/script.js"></script>
    
    <script>
        // Auto-dismiss notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            // Add close button to all alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (!alert.querySelector('.alert-close')) {
                    const closeBtn = document.createElement('span');
                    closeBtn.className = 'alert-close';
                    closeBtn.innerHTML = '×';
                    closeBtn.onclick = function() {
                        this.parentElement.style.display = 'none';
                    };
                    alert.appendChild(closeBtn);
                }
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>