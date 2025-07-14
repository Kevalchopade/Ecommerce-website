<?php 
include "../product/db.php";
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Handle quantity updates
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $item_id => $quantity) {
        $item_id = intval($item_id);
        $quantity = intval($quantity);

        if ($quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND username = ?");
            $stmt->bind_param("iis", $quantity, $item_id, $username);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND username = ?");
            $stmt->bind_param("is", $item_id, $username);
            $stmt->execute();
        }
    }
    header("Location: cart.php");
    exit();
}

// Handle item removal
if (isset($_GET['remove'])) {
    $item_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND username = ?");
    $stmt->bind_param("is", $item_id, $username);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Get cart items with product details
$stmt = $conn->prepare("SELECT cart.id AS cart_id, cart.quantity, cart.size, products.prod_id, products.name, products.price, products.image1
                        FROM cart 
                        JOIN products ON cart.prod_id = products.prod_id 
                        WHERE cart.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$cart_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommers</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link rel="stylesheet" href="/assets/css/styles.css"/>
    <link rel="stylesheet" href="/assets/css/login.css">
    <link rel="stylesheet" href="/assets/css/cart.css">
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
    <div class="cart-container">
      <h2>Your Shopping Cart</h2>
      
      <div id="cart-items" class="cart-items">
        <?php
        $total = 0;
        if($cart_result->num_rows > 0) {
            // Display cart items from database
            echo '<form method="post" action="">';
            while($row = $cart_result->fetch_assoc()) {
                $item_total = $row['price'] * $row['quantity'];
                $total += $item_total;
                ?>
                <div class="cart-item">
    <img src="/product/images/<?php echo $row['image1']; ?>" alt="<?php echo $row['name']; ?>">
    <div class="item-details">
        <h3><?php echo $row['name']; ?></h3>
        <p class="item-price">₹<?php echo number_format($row['price'], 2); ?></p>
        <?php if(!empty($row['size'])): ?>
        <p class="item-size">Size: <?php echo strtoupper($row['size']); ?></p>
        <?php endif; ?>
        <div class="quantity-control">
            <input type="number" name="quantity[<?php echo $row['cart_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1">
        </div>
        <p class="item-total">₹<?php echo number_format($item_total, 2); ?></p>
        <a href="cart.php?remove=<?php echo $row['cart_id']; ?>" class="remove-item">Remove</a>
    </div>
</div>
                <?php
            }
            echo '<div class="update-cart-container">
                    <button type="submit" name="update_cart" class="update-cart-btn">Update Cart</button>
                  </div>
                  </form>';
        } else {
            echo '<div class="empty-cart">
                    <p>Your cart is empty</p>
                    <a href="/product/shop.php" class="continue-shopping">Continue Shopping</a>
                  </div>';
        }
        ?>
      </div>
      
      <?php if($cart_result->num_rows > 0): ?>
      <div class="cart-summary">
        <div class="subtotal">
          <span>Subtotal:</span>
          <span id="cart-total">₹<?php echo number_format($total, 2); ?></span>
        </div>
        
        <div class="cart-buttons">
          <a href="/product/shop.php" class="continue-shopping">Continue Shopping</a>
          <button id="checkout-btn" class="checkout-btn">Proceed to Checkout</button>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </main>

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
  <script src="/assets/js/script.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Add event listener for checkout button
      const checkoutBtn = document.getElementById('checkout-btn');
      if(checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
          <?php if($cart_result->num_rows == 0): ?>
            alert('Your cart is empty');
            return;
          <?php else: ?>
            // Redirect to checkout page (to be implemented)
            alert('Proceeding to checkout...');
             window.location.href = 'checkout.php';
          <?php endif; ?>
        });
      }
    });
  </script>
  
</body>
</html>