<?php 
include "db.php"; 
session_start()
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartWear - Clothing Store</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link rel="stylesheet" href="/assets/css/shop.css" />
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <link rel="stylesheet" href="/assets/css/login.css" />
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
      <h1>Our Products</h1>
      
      <div class="filters">
        <!-- Filters will be populated by PHP -->
        <?php
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
?>

<select id="categoryFilter" name="category">
  <option value="" <?php if ($selectedCategory == '') echo 'selected'; ?>>All</option>
  <option value="men" <?php if ($selectedCategory == 'men') echo 'selected'; ?>>Men</option>
  <option value="women" <?php if ($selectedCategory == 'women') echo 'selected'; ?>>Women</option>
  <option value="accessories" <?php if ($selectedCategory == 'accessories') echo 'selected'; ?>>Accessories</option>
</select>

<?php
$selectedSubcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
?>

<select id="subcategoryFilter" name="subcategory">
  <option value="" <?php if ($selectedSubcategory == '') echo 'selected'; ?>>All</option>
  <option value="top-wear" <?php if ($selectedSubcategory == 'top-wear') echo 'selected'; ?>>Top-Wear</option>
  <option value="bottom-wear" <?php if ($selectedSubcategory == 'bottom-wear') echo 'selected'; ?>>Bottom-Wear</option>
</select>

      </div>

      <div id="products-container" class="products-grid">
        <!-- Products will be loaded here via AJAX -->
        <?php
        // Check if search term exists
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $search_term = mysqli_real_escape_string($conn, $_GET['search']);
            echo "<p class='search-results-info'>Showing results for: " . htmlspecialchars($search_term) . "</p>";
        }
        ?>
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
    // Search functionality
    const searchButton = document.getElementById('search-button');
    const searchOverlay = document.getElementById('search-overlay');
    const searchCloseBtn = document.getElementById('search-close-btn');
    const searchInput = document.getElementById('search-input-overlay');
    const searchSubmit = document.getElementById('search-submit');
    const wishlistAction = document.getElementById('wishlist-action');
    const cartAction = document.getElementById('cart-action');
    
    // Get URL params on page load
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');
    const subcategoryParam = urlParams.get('subcategory');
    const searchParam = urlParams.get('search');
    
    // Set filter dropdowns if they exist
    const categoryFilter = document.getElementById('categoryFilter');
    const subcategoryFilter = document.getElementById('subcategoryFilter');
    
    if (categoryParam && categoryFilter) {
        categoryFilter.value = categoryParam;
    }
    if (subcategoryParam && subcategoryFilter) {
        subcategoryFilter.value = subcategoryParam;
    }
    
    // Filter change events
    if(categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterProducts();
        });
    }
    
    if(subcategoryFilter) {
        subcategoryFilter.addEventListener('change', filterProducts);
    }
    
    // Load products with the URL parameters on page load
    loadProducts(categoryParam, subcategoryParam, searchParam);
    
    // Function to filter products based on dropdown selections
    function filterProducts() {
        const selectedCategory = categoryFilter ? categoryFilter.value : '';
        const selectedSubcategory = subcategoryFilter ? subcategoryFilter.value : '';
        
        // Update URL with new filter parameters
        const newUrl = new URL(window.location.href);
        if (selectedCategory) {
            newUrl.searchParams.set('category', selectedCategory);
        } else {
            newUrl.searchParams.delete('category');
        }
        
        if (selectedSubcategory) {
            newUrl.searchParams.set('subcategory', selectedSubcategory);
        } else {
            newUrl.searchParams.delete('subcategory');
        }
        
        // Keep search parameter if it exists
        if (searchParam) {
            newUrl.searchParams.set('search', searchParam);
        }
        
        // Update browser URL without reloading the page
        window.history.pushState({}, '', newUrl);
        
        // Load products with the selected filters
        loadProducts(selectedCategory, selectedSubcategory, searchParam);
    }
    
    // Function to load products via AJAX
    function loadProducts(category = '', subcategory = '', search = '') {
        // Show loading state
        const productsContainer = document.getElementById("products-container");
        if (productsContainer) {
            productsContainer.innerHTML = "<p>Loading products...</p>";
        }
        
        // Build query parameters
        const params = new URLSearchParams();
        if(category) params.append('category', category);
        if(subcategory) params.append('subcategory', subcategory);
        if(search) params.append('search', search);
        
        // Make AJAX request
        fetch(`get_products.php?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let productHTML = "";
                if(data.length === 0) {
                    productHTML = "<p>No products found matching your criteria.</p>";
                } else {
                    data.forEach(product => {
                        // In your loadProducts function in shop.php where you build the product cards
productHTML += `
    <div class="product-card" data-category="${product.category || ''}" data-subcategory="${product.subcategory || ''}">
        <a href="product.php?prod_id=${product.prod_id}" class="product-link">
            <div class="product-image-container">
                <div class="image-wrapper">
                    <img src="images/${product.image1}" alt="${product.name}" class="product-image primary-image">
                    <img src="images/${product.image2 || product.image1}" alt="${product.name}" class="product-image secondary-image">
                </div>
            </div>
            <div class="product-info">
                <h3 class="product-title">${product.name}</h3>
                <p class="product-price">₹${product.price}</p>
            </div>
        </a>
        <div class="product-buttons">
            <button class="circular-btn wishlist-btn" data-product-id="${product.prod_id}">
                <i class="fa fa-heart"></i>
            </button>
            <button class="circular-btn shop-btn add-to-cart" data-product-id="${product.prod_id}">
                <i class="fas fa-shopping-bag"></i>
            </button>
        </div>
    </div>
`;
                    });
                }
                
                if (productsContainer) {
                    productsContainer.innerHTML = productHTML;
                }
                
                // Initialize product buttons after loading products
                initProductButtons();
            })
            .catch(error => {
                console.error("Error fetching products:", error);
                if (productsContainer) {
                    productsContainer.innerHTML = "<p>Error loading products. Please try again later.</p>";
                }
            });
    }
    // Initialize product buttons (wishlist and add to cart)
function initProductButtons() {
    // Add to wishlist functionality
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Prevent event from bubbling up to the product link
            e.stopPropagation();
            e.preventDefault();
            
            const productId = this.dataset.productId;
            
            // AJAX request to add product to wishlist
            fetch('add_to_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Product added to wishlist!');
                } else if(data.message === 'Please log in to add items to wishlist') {
                    popup('login-popup');
                } else {
                    alert('Failed to add product to wishlist. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error adding to wishlist:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
    
    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Prevent event from bubbling up to the product link
            e.stopPropagation();
            e.preventDefault();
            
            const productId = this.dataset.productId;
            
            // AJAX request to add product to cart with default size "M" and quantity 1
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=1&size=M`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Product added to cart!');
                } else if(data.message === 'Please log in to add items to cart') {
                    popup('login-popup');
                } else {
                    alert(data.message || 'Failed to add product to cart. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
}
    
    // Other functions (popup, search, etc.) stay the same...
    
    // Function to show popup
    window.popup = function(id) {
        const popup = document.getElementById(id);
        if(popup) {
            popup.style.display = popup.style.display === 'flex' ? 'none' : 'flex';
        }
    };
});
</script>
  </body>
</html>