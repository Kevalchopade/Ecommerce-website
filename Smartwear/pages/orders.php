<?php 
include "../product/db.php";
session_start();

// Check if user is logged in, redirect if not
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    $_SESSION['login_required'] = "Please login to view your orders";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['username']; // Get the username which is used as user_id

// Fetch all orders for the current user
$orders = [];
$stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY order_date DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    // Get order items for this order
    $order_id = $row['order_id'];
    $items_stmt = $conn->prepare("SELECT oi.*, p.name, p.image1 
                                FROM order_items oi 
                                JOIN products p ON oi.prod_id = p.prod_id 
                                WHERE oi.order_id = ?");
    $items_stmt->bind_param("s", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $items = [];
    while($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
    
    $row['items'] = $items;
    $orders[] = $row;
}

// Define which order statuses can be cancelled
$cancellable_statuses = ['Pending', 'Processing'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders - SmartWear</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="stylesheet" href="/assets/css/orders.css">
  <link rel="stylesheet" href="/assets/css/login.css">
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

  <main>
    <div class="orders-container">
      <h2>My Orders</h2>
      
      <?php if(isset($_SESSION['order_success'])): ?>
        <div class="order-success">
          <?php 
            echo $_SESSION['order_success']; 
            unset($_SESSION['order_success']); 
          ?>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_SESSION['order_error'])): ?>
        <div class="order-error">
          <?php 
            echo $_SESSION['order_error']; 
            unset($_SESSION['order_error']); 
          ?>
        </div>
      <?php endif; ?>
      
      <?php if(count($orders) > 0): ?>
        <div class="orders-list">
          <?php foreach($orders as $order): ?>
            <div class="order-card">
              <div class="order-header">
                <div class="order-info">
                  <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                  <p class="order-date">Placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                </div>
                <div class="order-actions">
                  <div class="order-status <?php echo strtolower($order['status']); ?>">
                    <?php echo htmlspecialchars($order['status']); ?>
                  </div>
                  <?php if(in_array($order['status'], $cancellable_statuses)): ?>
                    <button class="cancel-order-btn" 
                            onclick="confirmCancelOrder('<?php echo $order['order_id']; ?>')">
                      Cancel Order
                    </button>
                  <?php endif; ?>
                </div>
              </div>
              
              <div class="order-items">
                <?php foreach($order['items'] as $item): ?>
                  <div class="order-item">
                    <div class="item-image">
                      <img src="/product/images/<?php echo htmlspecialchars($item['image1']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="item-details">
                      <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                      <?php if(!empty($item['size'])): ?>
                        <p class="item-size">Size: <?php echo strtoupper($item['size']); ?></p>
                      <?php endif; ?>
                      <p>Quantity: <?php echo $item['quantity']; ?></p>
                      <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <div class="order-footer">
                <div class="order-address">
                  <h4>Shipping Address:</h4>
                  <p><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                  <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                  <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                </div>
                <div class="order-summary">
                  <div class="summary-line">
                    <span>Subtotal:</span>
                    <span>₹<?php echo number_format($order['subtotal'], 2); ?></span>
                  </div>
                  <div class="summary-line">
                    <span>Shipping:</span>
                    <span>₹<?php echo number_format($order['shipping_fee'], 2); ?></span>
                  </div>
                  <div class="summary-line">
                    <span>Tax:</span>
                    <span>₹<?php echo number_format($order['tax'], 2); ?></span>
                  </div>
                  <div class="summary-total">
                    <span>Total:</span>
                    <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="no-orders">
          <p>You don't have any orders yet.</p>
          <a href="/product/shop.php" class="continue-shopping">Start Shopping</a>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Confirmation Modal -->
  <div id="confirmationModal" class="modal">
    <div class="modal-content">
      <h3>Cancel Order</h3>
      <p>Are you sure you want to cancel this order?</p>
      <p>This action cannot be undone.</p>
      <div class="modal-actions">
        <button id="cancelNo" class="modal-btn secondary-btn">No, Keep Order</button>
        <button id="cancelYes" class="modal-btn primary-btn">Yes, Cancel Order</button>
      </div>
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
        <p>Enter your email address below and we'll send you a link to reset your password.</p>
        <form id="forgotPasswordForm" method="POST" action="../product/reset_password_request.php">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="reset-request-submit" class="forget-password-button">Send Reset Link</button>
        </form>
    </div>
</div>

<!-- Add Reset Password Popup -->
<div id="resetPasswordPopup" class="popup-container">
    <div class="popup">
        <span class="register-close-btn" onclick="closeResetPasswordPopup()">&times;</span>
        <h2 class="reset-password-headline">SET NEW PASSWORD</h2>
        <form id="resetPasswordForm" method="POST" action="../product/reset_password.php">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <input type="hidden" name="selector" id="resetSelector" value="">
            <input type="hidden" name="validator" id="resetValidator" value="">
            <button type="submit" name="reset-password-submit" class="reset-password-button">Reset Password</button>
        </form>
    </div>
</div>

<!-- Size Guide Popup -->
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
  // Function to handle login popup
  function popup(id) {
    const popup = document.getElementById(id);
    if (popup) {
      popup.style.display = popup.style.display === 'flex' ? 'none' : 'flex';
    }
  }
  
  // Variables for order cancellation
  const modal = document.getElementById('confirmationModal');
  const cancelYesBtn = document.getElementById('cancelYes');
  const cancelNoBtn = document.getElementById('cancelNo');
  let currentOrderId = null;
  
  // Function to show cancel order confirmation
  function confirmCancelOrder(orderId) {
    currentOrderId = orderId;
    modal.style.display = 'flex';
  }
  
  // Close modal when clicking "No"
  cancelNoBtn.addEventListener('click', function() {
    modal.style.display = 'none';
  });
  
// Proceed with cancellation when clicking "Yes"
cancelYesBtn.addEventListener('click', function() {
  if (currentOrderId) {
    window.location.href = 'cancle_order.php?order_id=' + currentOrderId;
  }
});
  
  // Close modal if user clicks outside of it
  window.addEventListener('click', function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });
  </script>
  <script src="/assets/js/script.js"></script>
</body>
</html>