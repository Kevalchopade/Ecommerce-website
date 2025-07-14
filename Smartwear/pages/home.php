<?php include('../product/db.php');
session_start()
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
                      <li><a href="/product/shop.php?category=accessories&subcategory=Bag">Bag</a></li>
                      <li><a href="/product/shop.php?category=accessories&subcategory=Bracelet">Bracelet</a></li>
                      <li><a href="/product/shop.php?category=accessories&subcategory=Chain">Chain</a></li>
                      <li><a href="/product/shop.php?category=accessories&subcategory=Ring">Ring</a></li>
                      <li><a href="/product/shop.php?category=accessories&subcategory=Watch">Watch</a></li>
                      <li><a href="/product/shop.php?category=accessories&subcategory=Earring">Earring</a></li>
                      
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
    
    <section id="hero">
      <h4>Smart Wear</h4>
      <h2>Where Style Meets</h2>
      <h1>Confidence!</h1>
      <button>Shop Now</button>
    </section>


    <section id="product1" class="section-p1">
      <h2>Featured Topwear</h2>
      <p>Summer Collection New Morden Design</p>
      <div class="pro-container">
        <div class="pro">
          <img src="/product/images/shirtF3.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Brown Floral Print Shirt</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>499</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/Ot-shirtB15.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Beige Pintuck Oversized T-shirt</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>699</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/Ot-shirtB11.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Sew Panel Oversized T-Shirt</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>749</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/shirtF4.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Vintage Floral Retro Shirt</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>399</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/shirtF21.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Printed Asymmetrical Cotton Shirt</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>550</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/topF1.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Strapless Ribbed Top</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>690</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/topF3.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Backless Boat Neck Top
            </h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>720</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/topF9.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Printed Draped Crop Top</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>720</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
      </div>
    </section>

    <section id="banner" class="section-m1">
      <h4>Check Accessories</h4>
      <h2>All New <span>Arrivals</span> Are Here</h2>
      <a href="#"><button class="normal">Explore More</button></a>
    </section>

    <section id="product1" class="section-p1">
      <h2>Featured Bottomwear</h2>
      <p>Summer Collection New Morden Design</p>
      <div class="pro-container">
        <div class="pro">
          <img src="/product/images/cargoF1.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Beige Cotton Cargo Joggers</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>1199</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/JeansF3.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Ice Blue Regular Fit Jeans</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>1199</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/jeansF5.jpg" alt="" />
          <div class="des">
            <span>Smart Wera</span>
            <h5>Black Regular Fit Jeans</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>1189</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/cargoF6.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>White Statement Cargo Pants</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>1999</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/skirtF11.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Pleated Midi Skirt</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>798</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/cargoF21.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>The Parachute Pant</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>$78</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/cargoF17.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>The Cargo Pant</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>1199</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
        <div class="pro">
          <img src="/product/images/skirtF9.jpg" alt="" />
          <div class="des">
            <span>Smart Wear</span>
            <h5>Pleated Midi Skirt</h5>
            <div class="star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <h4>799</h4>
          </div>
          <a href="/product/shop.php"><i class="fas fa-shopping-cart cart"></i></a>
        </div>
      </div>
    </section>

    <section id="sm-banner" class="section-p1">
      <div class="banner-box">
        <h4>Crazy deals</h4>
        <h2>buy 1 get 1 free</h2>
        <span>The best classic dress is on sale at care</span>
        <button class="white">Learn More</button>
      </div>
      <div class="banner-box banner-box2">
        <h4>Spring/Summer</h4>
        <h2>Upcomming Season</h2>
        <span>The best classic dress is on sale at care</span>
        <button class="white">Collection</button>
      </div>
    </section>

    <section id="banner3">
      <div class="banner-box">
        <h2>SEASONAL SALE</h2>
        <h3>Winter Collection -50% Off</h3>
      </div>
      <div class="banner-box banner-box2">
        <h2>NEW FOOTWARE COLLECTION</h2>
        <h3>Spring/Summer 2022</h3>
      </div>
      <div class="banner-box banner-box3">
        <h2>T-SHIRTS</h2>
        <h3>New Trendy Prints</h3>
      </div>
    </section>

    <section id="newsletter" class="section-p1 section-m1">
      <div class="newstext">
        <h4>Sign Up For Newsletters</h4>
        <p>
          Get E-mail updates about our latest shop and
          <span>special offers.</span>
        </p>
      </div>
      <div class="form">
        <input type="text" placeholder="Your email address" />
        <button class="normal">Sign Up</button>
      </div>
    </section>

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

   <!-- Forgot Password Popup -->
<div id="forgotPasswordContainer" class="popup-container">
    <div class="popup">
        <span class="register-close-btn" onclick="closeForgotPasswordPopup()">&times;</span>
        <h2 class="forget-password-headline">RESET PASSWORD</h2>
        <p>Enter your email address to verify your account and reset your password.</p>
        <form id="forgotPasswordForm">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" class="forget-password-button">Verify</button>
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

</body>
</html>