<?php 
include "../product/db.php";
session_start();

// Initialize variables
$cart_items = [];
$total_price = 0;
$tax_rate = 0.18; // 18% tax
$shipping_fee = 50; // Fixed shipping fee

// Check if user is logged in, redirect if not
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    // Redirect to login or show message
    $_SESSION['checkout_error'] = "Please login to continue with checkout";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['username']; // Get the username which is used as user_id

if(isset($_GET['prod_id'])) {
  $prod_id = intval($_GET['prod_id']);
  $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
  $size = isset($_GET['size']) ? $_GET['size'] : null; // Add this line to get size parameter
  
  // Fetch product details from database
  $stmt = $conn->prepare("SELECT * FROM products WHERE prod_id = ?");
  $stmt->bind_param("i", $prod_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if($result->num_rows > 0) {
      $product = $result->fetch_assoc();
  // In checkout.php, modify the cart_items array to include size:
$cart_items[] = [
  'id' => $row['prod_id'],
  'name' => $row['name'],
  'price' => $row['price'],
  'image' => $row['image1'],
  'quantity' => $row['quantity'],
  'size' => $row['size'] // Add this line
];
      $total_price = $product['price'] * $quantity;
  }
}
// Case 2: Coming from cart page
else {
  // Fetch cart items from database for the current user
  $stmt = $conn->prepare("SELECT cart.id AS cart_id, cart.quantity, cart.size, products.prod_id, products.name, products.price, products.image1 
                         FROM cart 
                         JOIN products ON cart.prod_id = products.prod_id 
                         WHERE cart.username = ?");
  $stmt->bind_param("s", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  while($row = $result->fetch_assoc()) {
      $cart_items[] = [
          'id' => $row['prod_id'],
          'name' => $row['name'],
          'price' => $row['price'],
          'image' => $row['image1'],
          'quantity' => $row['quantity'],
          'size' => $row['size'] // Add size to the cart item
      ];
      $total_price += $row['price'] * $row['quantity'];
  }
}

// Calculate tax and total
$tax_amount = $total_price * $tax_rate;
$grand_total = $total_price + $tax_amount + $shipping_fee;

// Convert cart items to JSON for JavaScript
$cart_items_json = json_encode($cart_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - SmartWear</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="stylesheet" href="/assets/css/cart.css">
  <link rel="stylesheet" href="/assets/css/checkout.css">
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
    <div class="checkout-container">
      <h2>Checkout</h2>
      
      <div class="checkout-grid">
        <!-- Left side: Customer Information -->
        <div class="checkout-information">
        <form id="checkout-form" method="POST" action="process_order.php">
            <!-- Add hidden fields to pass cart data -->
            <input type="hidden" name="cart_data" value='<?php echo htmlspecialchars($cart_items_json); ?>'>
            <input type="hidden" name="subtotal" value="<?php echo $total_price; ?>">
            <input type="hidden" name="tax" value="<?php echo $tax_amount; ?>">
            <input type="hidden" name="shipping" value="<?php echo $shipping_fee; ?>">
            <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
            <input type="hidden" name="payment_method" id="payment_method_input" value="cod">
            
            <div class="checkout-section">
              <h3>Contact Information</h3>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
              </div>
              <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" required>
              </div>
            </div>
            
            <div class="checkout-section">
              <h3>Shipping Address</h3>
              <div class="form-row">
                <div class="form-group">
                  <label for="firstName">First Name</label>
                  <input type="text" id="firstName" name="firstName" required>
                </div>
                <div class="form-group">
                  <label for="lastName">Last Name</label>
                  <input type="text" id="lastName" name="lastName" required>
                </div>
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                
                <input type="text" id="address" name="address" required>
              </div>
              <div class="form-group">
                <label for="apartment">Apartment, suite, etc. (optional)</label>
                <input type="text" id="apartment" name="apartment">
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" id="city" name="city" required>
                </div>
                <div class="form-group">
                  <label for="state">State</label>
                  <select id="state" name="state" required>
                    <option value="">Select State</option>
                    <option value="AP">Andhra Pradesh</option>
                    <option value="AR">Arunachal Pradesh</option>
                    <option value="AS">Assam</option>
                    <option value="BR">Bihar</option>
                    <option value="CT">Chhattisgarh</option>
                    <option value="GA">Goa</option>
                    <option value="GJ">Gujarat</option>
                    <option value="HR">Haryana</option>
                    <option value="HP">Himachal Pradesh</option>
                    <option value="JK">Jammu and Kashmir</option>
                    <option value="JH">Jharkhand</option>
                    <option value="KA">Karnataka</option>
                    <option value="KL">Kerala</option>
                    <option value="MP">Madhya Pradesh</option>
                    <option value="MH">Maharashtra</option>
                    <option value="MN">Manipur</option>
                    <option value="ML">Meghalaya</option>
                    <option value="MZ">Mizoram</option>
                    <option value="NL">Nagaland</option>
                    <option value="OR">Odisha</option>
                    <option value="PB">Punjab</option>
                    <option value="RJ">Rajasthan</option>
                    <option value="SK">Sikkim</option>
                    <option value="TN">Tamil Nadu</option>
                    <option value="TG">Telangana</option>
                    <option value="TR">Tripura</option>
                    <option value="UT">Uttarakhand</option>
                    <option value="UP">Uttar Pradesh</option>
                    <option value="WB">West Bengal</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="zipcode">PIN Code</label>
                  <input type="text" id="zipcode" name="zipcode" required>
                </div>
              </div>
            </div>
            
            <div class="checkout-section">
  <h3>Payment Method</h3>
  <div class="payment-methods">
    <div class="payment-method">
      <input type="radio" id="payment-cod" name="payment-method" value="cod" checked>
      <label for="payment-cod">Cash on Delivery</label>
    </div>
    <div class="payment-method">
      <input type="radio" id="payment-qr" name="payment-method" value="qr">
      <label for="payment-qr">Pay Using QR</label>
    </div>
  </div>
</div>

            
            <div class="checkout-section">
              <div class="terms-checkbox">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></label>
              </div>
            </div>
            
            <button type="submit" id="place-order-btn" class="place-order-btn">Place Order</button>
          </form>
        </div>
        
        <!-- Right side: Order Summary -->
        <div class="order-summary">
          <h3>Order Summary</h3>
          <div id="checkout-items" class="checkout-items">
            <?php if(count($cart_items) > 0): ?>
              <?php foreach($cart_items as $item): ?>
                <div class="checkout-item">
                  <div class="checkout-item-image">
                    <img src="/product/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                  </div>
                  <div class="checkout-item-details">
                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                    <div class="checkout-item-price">
                      <span>₹<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?></span>
                    </div>
                  </div>
                  <div class="checkout-item-total">
                    ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="empty-checkout">
                <p>No items in your checkout</p>
                <a href="/product/shop.php" class="continue-shopping">Continue Shopping</a>
              </div>
            <?php endif; ?>
          </div>
          
          <?php if(count($cart_items) > 0): ?>

          
          <div class="summary-line">
            <span>Subtotal</span>
            <span id="summary-subtotal">₹<?php echo number_format($total_price, 2); ?></span>
          </div>
          <div class="summary-line">
            <span>Shipping</span>
            <span id="summary-shipping">₹<?php echo number_format($shipping_fee, 2); ?></span>
          </div>
          <div class="summary-line">
            <span>Tax (18%)</span>
            <span id="summary-tax">₹<?php echo number_format($tax_amount, 2); ?></span>
          </div>
          <div class="summary-total">
            <span>Total</span>
            <span id="summary-total">₹<?php echo number_format($grand_total, 2); ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <!-- QR Code Payment Popup -->
  <div id="qr-payment-popup" class="qr-popup-container">
    <div class="qr-popup">
      <span class="qr-popup-close">&times;</span>
      <h3>Scan QR Code to Pay</h3>
      <div class="qr-code-container">
        <!-- QR code will be generated here -->
        <div id="qr-code"></div>
      </div>
      <div class="timer-container">
        <div class="timer-bar">
          <div class="timer-progress" id="timer-progress"></div>
        </div>
        <div class="timer-text">Time remaining: <span id="timer-seconds">10</span> seconds</div>
      </div>
    </div>
  </div>

  <!-- Notification container -->
  <div class="notification-container" id="notification-container"></div>

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

  <!-- Include QR code library -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  
  <script>
  // Function to handle login popup
  function popup(id) {
    const popup = document.getElementById(id);
    if (popup) {
      popup.style.display = popup.style.display === 'flex' ? 'none' : 'flex';
    }
  }

  // QR Payment functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const qrPaymentRadio = document.getElementById('payment-qr');
    const codPaymentRadio = document.getElementById('payment-cod');
    const qrPopup = document.getElementById('qr-payment-popup');
    const qrPopupClose = document.querySelector('.qr-popup-close');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const paymentMethodInput = document.getElementById('payment_method_input');
    const checkoutForm = document.getElementById('checkout-form');
    
    // Form validation
    if (checkoutForm) {
      checkoutForm.addEventListener('submit', function(e) {
        const termsCheckbox = document.getElementById('terms');
        if (!termsCheckbox.checked) {
          e.preventDefault();
          showNotification('You must agree to the Terms and Conditions', 'error');
          return;
        }
        
        // If QR payment is selected, show QR popup and prevent form submission
        if (qrPaymentRadio.checked) {
          e.preventDefault();
          showQRPaymentPopup();
        } else {
          // For COD, update payment method and submit form
          paymentMethodInput.value = 'cod';
        }
      });
    }
    
    // Update payment method when radio button changes
    qrPaymentRadio.addEventListener('change', function() {
      if (this.checked) {
        paymentMethodInput.value = 'qr';
      }
    });
    
    codPaymentRadio.addEventListener('change', function() {
      if (this.checked) {
        paymentMethodInput.value = 'cod';
      }
    });
    
    // Close QR popup when clicking on close button
    if (qrPopupClose) {
      qrPopupClose.addEventListener('click', function() {
        closeQRPopup();
        showNotification('Payment cancelled', 'error');
      });
    }
    
    // Function to show QR payment popup
    function showQRPaymentPopup() {
      const totalAmount = <?php echo json_encode($grand_total); ?>;
      const orderId = 'ORD' + Date.now();
      
      // Display the popup
      qrPopup.style.display = 'flex';
      
      // Generate QR code with payment details
      const qrCodeContainer = document.getElementById('qr-code');
      qrCodeContainer.innerHTML = ''; // Clear previous QR code if any
      
      // Create an image element for the QR code
      const qrImg = document.createElement('img');
      qrImg.src = `generate_upi_qr.php?amount=${totalAmount}&order_id=${orderId}`;
      qrImg.alt = 'UPI Payment QR Code';
      qrImg.style.width = '250px';
      qrImg.style.height = '250px';
      
      // Add the image to the container
      qrCodeContainer.appendChild(qrImg);
      
      // Start the timer
      startQRTimer();
    }

    // Function to close QR popup
    function closeQRPopup() {
      qrPopup.style.display = 'none';
      // Reset timer
      clearTimeout(qrTimerTimeout);
      const timerProgress = document.getElementById('timer-progress');
      const timerSeconds = document.getElementById('timer-seconds');
      timerProgress.style.width = '100%';
      timerSeconds.textContent = '180'; // Updated to 180 seconds
    }
    
    // Timer variables
    let qrTimerTimeout;
    let secondsLeft = 180; // 180 seconds timer
    
    // Function to start timer for QR code popup
    function startQRTimer() {
      secondsLeft = 180;
      const timerProgress = document.getElementById('timer-progress');
      const timerSeconds = document.getElementById('timer-seconds');
      
      // Update timer every second
      function updateTimer() {
        secondsLeft--;
        timerSeconds.textContent = secondsLeft;
        
        // Update progress bar
        const widthPercentage = (secondsLeft / 180) * 100;
        timerProgress.style.width = widthPercentage + '%';
        
        if (secondsLeft <= 0) {
          // Time's up - close popup and show notification
          closeQRPopup();
          showNotification('Payment time expired', 'error');
        } else {
          qrTimerTimeout = setTimeout(updateTimer, 1000);
        }
      }
      
      // Start the timer
      updateTimer();
      
      // For demonstration, simulate successful payment after 10 seconds
      // In a real app, you would check for payment confirmation from your payment gateway
      setTimeout(function() {
        // Only process if the popup is still open
        if (qrPopup.style.display === 'flex') {
          processSuccessfulPayment();
        }
      }, 10000); // Exactly 10 seconds
    }
    
    // Function to process successful payment
    function processSuccessfulPayment() {
      // Close the QR popup
      closeQRPopup();
      
      // Show success notification
      showNotification('Payment successful! Processing your order...', 'success');
      
      // Submit the form after a short delay
      setTimeout(function() {
        paymentMethodInput.value = 'qr';
        checkoutForm.submit();
      }, 1500);
    }
    
    // Function to show notifications
    function showNotification(message, type) {
      const notificationContainer = document.getElementById('notification-container');
      
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `notification ${type}`;
      
      // Add icon based on type
      let icon = '';
      if (type === 'success') {
        icon = '<i class="fas fa-check-circle"></i>';
      } else if (type === 'error') {
        icon = '<i class="fas fa-exclamation-circle"></i>';
      }
      
      // Set notification content
      notification.innerHTML = `
        ${icon}
        <span>${message}</span>
      `;
      
      // Add to container
      notificationContainer.appendChild(notification);
      
      // Show notification with animation
      setTimeout(() => {
        notification.classList.add('show');
      }, 10);
      
      // Remove after 5 seconds
      setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
          notification.remove();
        }, 300);
      }, 5000);
    }
  });
</script>
  <script src="/assets/js/script.js"></script>
</body>
</html>