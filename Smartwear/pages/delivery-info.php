<?php
include('../product/db.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Delivery Information | Smart Wear</title>

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      margin: 0;
      padding: 20px;
      background-color: #f2f2f2;
      color: #333;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    h3 {
      color: #2e3b4e;
      margin-top: 25px;
    }

    p, li {
      font-size: 16px;
    }

    ul {
      padding-left: 20px;
    }

    footer {
      text-align: center;
      margin-top: 50px;
      font-size: 0.9em;
      color: #777;
    }
  </style>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
  <link rel="stylesheet" href="/assets/css/styles.css"/>
  <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>

<!-- Navbar -->
<section id="header">
  <a href="#"><img src="/assets/img/logo.png" alt="" /></a>

  <div>
    <ul id="navbar">
      <li class="dropdown">
        <a href="#" class="dropdown-btn">Men</a>
        <ul class="dropdown-menu">
          <li><a href="/product/shop.php?category=men&subcategory=top-wear">Upperwear</a></li>
          <li><a href="/product/shop.php?category=men&subcategory=bottom-wear">Lowerwear</a></li>
        </ul>
      </li>
      <li class="dropdown"> 
        <a href="#" class="dropdown-btn">Women</a>
        <ul class="dropdown-menu">
          <li><a href="/product/shop.php?category=women&subcategory=top-wear">Upperwear</a></li>
          <li><a href="/product/shop.php?category=women&subcategory=bottom-wear">Lowerwear</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" class="dropdown-btn">Accessories</a>
        <ul class="dropdown-menu">
          <li><a href="/product/shop.php?category=accessories&subcategory=men">Man</a></li>
          <li><a href="/product/shop.php?category=accessories&subcategory=women">Women</a></li>
        </ul>
      </li>

      <li id="lg-dash"><i>|</i></li>
      <li id="lg-wishlist"><a href="/pages/wishlist.php"><i class="fa fa-heart"></i></a></li>
      <li id="lg-bag"><a href="/pages/cart.php"><i class="fas fa-shopping-bag"></i></a></li>

      <li id="lg-profile" class="profile-dropdown">
        <button id="profile-btn"><i class="fas fa-user"></i></button>
        <ul class="dropdown-menu" id="profile-menu">
          <?php
          if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
              echo "<li><a href='profile.php'>My Profile</a></li>";
              echo "<li><a href='wishlist.php'>Wishlist</a></li>";
              echo "<li><a href='orders.php'>My Orders</a></li>";
              echo "<li><a href='contact.php'>Contact Us</a></li>";
              echo "<li class='user'><span>$_SESSION[username]</span> - <a href='../product/logout.php'>Logout</a></li>";
          } else {
              echo "<li class='sign-in'><a href='#' onclick=\"popup('login-popup')\">Log in</a></li>";
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

<!-- Delivery Information Content -->
<div class="container">
  <h1>Delivery Information</h1>
  <p><strong>Effective Date:</strong> April 2025</p>

  <h3>1. Delivery Options</h3>
  <p>We offer the following delivery options to meet your needs:</p>
  <ul>
    <li>Standard Delivery (3–7 Business Days)</li>
    <li>Express Delivery (1–3 Business Days)</li>
    <li>Same-Day Delivery (Available in select cities)</li>
  </ul>

  <h3>2. Shipping Charges</h3>
  <ul>
    <li>Free Standard Delivery on orders above ₹999</li>
    <li>Express Delivery: ₹150</li>
    <li>Same-Day Delivery: ₹250</li>
  </ul>

  <h3>3. Order Processing</h3>
  <p>Orders are typically processed within 24–48 business hours. Once processed, you will receive a tracking number via SMS or email.</p>

  <h3>4. Tracking Your Order</h3>
  <p>You can track your order using the link in your confirmation email or via the “My Orders” section on your profile.</p>

  <h3>5. Delivery Locations</h3>
  <p>We currently deliver all over India. International shipping is not available at this time.</p>

  <h3>6. Delays & Issues</h3>
  <p>In case of delays due to weather, strikes, or other external factors, we’ll notify you through your registered contact details.</p>

  <h3>7. Need Help?</h3>
  <p>Reach out to us at <strong>support@smartwear.com</strong> or call <strong>+91-9876543210</strong>.</p>
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
</body>
</html>