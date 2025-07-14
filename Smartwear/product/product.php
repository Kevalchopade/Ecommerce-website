<?php
    include "db.php";
    session_start();

    // Get product ID from URL
    if (!isset($_GET['prod_id']) || empty($_GET['prod_id'])) {
        header("Location: shop.php");
        exit();
    }

    $prod_id = intval($_GET['prod_id']);

    // Get product details from database
    $stmt = $conn->prepare("SELECT * FROM products WHERE prod_id = ?");
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: shop.php");
        exit();
    }

    $product = $result->fetch_assoc();

    // Check if product is in user's wishlist (if logged in)
    $in_wishlist = false;
    $in_cart = false;

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $username = $_SESSION['username'];
        
        // Check wishlist
        $stmt = $conn->prepare("SELECT * FROM wishlist WHERE username = ? AND prod_id = ?");
        $stmt->bind_param("si", $username, $prod_id);
        $stmt->execute();
        $wishlist_result = $stmt->get_result();
        $in_wishlist = ($wishlist_result->num_rows > 0);
        
        // Check cart
        $stmt = $conn->prepare("SELECT * FROM cart WHERE username = ? AND prod_id = ?");
        $stmt->bind_param("si", $username, $prod_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();
        $in_cart = ($cart_result->num_rows > 0);
    }

    // Get related products (same category)
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND prod_id != ? LIMIT 4");
    $stmt->bind_param("si", $product['category'], $prod_id);
    $stmt->execute();
    $related_products = $stmt->get_result();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($product['name']); ?> - SmartWear</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
        <link rel="stylesheet" href="/assets/css/styles.css">
        <link rel="stylesheet" href="/assets/css/product.css">
        <link rel="stylesheet" href="/assets/css/prod_detail.css">
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

        <section id="product-detail" class="section-p1">
            <div class="product-images">
                <div class="main-image">
                    <img src="images/<?php echo htmlspecialchars($product['image1']); ?>" id="main-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="small-images">
                    <?php if (!empty($product['image1'])): ?>
                    <div class="small-img-col">
                        <img src="images/<?php echo htmlspecialchars($product['image1']); ?>" class="small-img" alt="Image 1">
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['image2'])): ?>
                    <div class="small-img-col">
                        <img src="images/<?php echo htmlspecialchars($product['image2']); ?>" class="small-img" alt="Image 2">
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['image3'])): ?>
                    <div class="small-img-col">
                        <img src="images/<?php echo htmlspecialchars($product['image3']); ?>" class="small-img" alt="Image 3">
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['image4'])): ?>
                    <div class="small-img-col">
                        <img src="images/<?php echo htmlspecialchars($product['image4']); ?>" class="small-img" alt="Image 4">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-details">
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                <h2>₹<?php echo htmlspecialchars($product['price']); ?></h2>
                
                <?php if (!empty($product['size'])): ?>
<div class="size-options">
    <h5>Select Size</h5>
    <div class="size-buttons">
        <?php 
        $size_range = $product['size'];
        // Check if the size string contains a range (e.g., "s-xxl")
        if (strpos($size_range, '-') !== false) {
            // Define the standard size order
            $all_sizes = ['s', 'm', 'l', 'xl', 'xxl'];
            // Split the range (e.g., "s-xxl" becomes ["s", "xxl"])
            $size_limits = explode('-', strtolower($size_range));
            
            // Find the start and end index in our standard size array
            $start_index = array_search($size_limits[0], $all_sizes);
            $end_index = array_search($size_limits[1], $all_sizes);
            
            // Create an array with just the sizes in our range
            $available_sizes = array_slice($all_sizes, $start_index, $end_index - $start_index + 1);
            
            // Display a button for each available size
            foreach ($available_sizes as $size):
                $display_size = strtoupper($size);
        ?>
                <button class="size-btn" data-size="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($display_size); ?></button>
        <?php 
            endforeach;
        } else {
            // If it's not a range, handle individual sizes
            $sizes = explode(',', $product['size']);
            foreach ($sizes as $size): 
                $size = trim($size);
                $display_size = strtoupper($size);
        ?>
                <button class="size-btn" data-size="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($display_size); ?></button>
        <?php 
            endforeach;
        }
        ?>
    </div>
</div>
<?php endif; ?>
                
                <div class="quantity">
                    <h5>Quantity</h5>
                    <div class="quantity-selector">
                        <button id="decrease-quantity">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="10">
                        <button id="increase-quantity">+</button>
                    </div>
                </div>
                
                <div class="product-actions">
                    <button id="add-to-cart" class="primary-btn <?php echo $in_cart ? 'in-cart' : ''; ?>" data-product-id="<?php echo $prod_id; ?>">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                    <button id="add-to-wishlist" class="secondary-btn <?php echo $in_wishlist ? 'in-wishlist' : ''; ?>" data-product-id="<?php echo $prod_id; ?>">
                        <i class="fa fa-heart"></i> <?php echo $in_wishlist ? 'In Wishlist' : 'Add to Wishlist'; ?>
                    </button>
                    <button id="buy-now" class="primary-btn accent">Buy Now</button>
                </div>
                
                <div class="product-description">
                    <h5>Product Details</h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
            </div>
        </section>

        <section id="related-products" class="section-p1">
            <h2>Related Products</h2>
            <div class="products-container">
                <?php while ($related = $related_products->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="product.php?prod_id=<?php echo $related['prod_id']; ?>">
                        <img src="images/<?php echo htmlspecialchars($related['image1']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                        <div class="product-info">
                            <h4><?php echo htmlspecialchars($related['name']); ?></h4>
                            <p>₹<?php echo htmlspecialchars($related['price']); ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
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
                    <a href="#" class="forgot-password">Forgot Password</a>
                    <a href="#" class="create-account">Creat Account</a>
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
            <form id="forgotPasswordForm" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" id="nextButton" class="forget-password-button">Send Reset Link</button>
            </form>
        </div>
    </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Image gallery functionality
                const mainImg = document.getElementById('main-img');
                const smallImgs = document.querySelectorAll('.small-img');
                
                smallImgs.forEach(img => {
                    img.addEventListener('click', function() {
                        mainImg.src = this.src;
                    });
                });
                
                // Size selection
                const sizeButtons = document.querySelectorAll('.size-btn');
                let selectedSize = null;
                
                sizeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        sizeButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        selectedSize = this.dataset.size;
                    });
                });
                
                // Quantity controls
                const quantityInput = document.getElementById('quantity');
                const decreaseBtn = document.getElementById('decrease-quantity');
                const increaseBtn = document.getElementById('increase-quantity');
                
                decreaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    if (value > 1) {
                        quantityInput.value = value - 1;
                    }
                });
                
                increaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    if (value < 10) {
                        quantityInput.value = value + 1;
                    }
                });
                
               // Add to cart functionality
const addToCartBtn = document.getElementById('add-to-cart');

addToCartBtn.addEventListener('click', function() {
    const productId = this.dataset.productId;
    const quantity = parseInt(quantityInput.value);
    
    if (<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true ? 'true' : 'false'; ?>) {
        // Check if size is selected when sizes are available
        const sizeButtons = document.querySelectorAll('.size-btn');
        const activeSizeBtn = document.querySelector('.size-btn.active');
        
        if (sizeButtons.length > 0 && !activeSizeBtn) {
            showNotification('Please select a size first!');
            return;
        }
        
        let selectedSize = null;
        if (activeSizeBtn) {
            selectedSize = activeSizeBtn.dataset.size;
        }
        
        // User is logged in, proceed with AJAX
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            // Change this line:
            body: `product_id=${productId}&quantity=${quantity}${selectedSize ? `&size=${selectedSize}` : ''}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Product added to cart!');
                addToCartBtn.classList.add('in-cart');
            } else {
                showNotification(data.message || 'Failed to add product to cart.');
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            showNotification('An error occurred. Please try again.');
        });
    } else {
        // User is not logged in, show login popup
        popup('login-popup');
    }
});

// Add to wishlist functionality
const wishlistBtn = document.getElementById('add-to-wishlist');

wishlistBtn.addEventListener('click', function() {
    const productId = this.dataset.productId;
    
    if (<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true ? 'true' : 'false'; ?>) {
        // User is logged in, proceed with AJAX
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Product added to wishlist!');
                wishlistBtn.classList.add('in-wishlist');
                wishlistBtn.innerHTML = '<i class="fa fa-heart"></i> In Wishlist';
            } else {
                showNotification(data.message || 'Failed to add product to wishlist.');
            }
        })
        .catch(error => {
            console.error('Error adding to wishlist:', error);
            showNotification('An error occurred. Please try again.');
        });
    } else {
        // User is not logged in, show login popup
        popup('login-popup');
    }
});

// Buy now button
const buyNowBtn = document.getElementById('buy-now');

buyNowBtn.addEventListener('click', function() {
    const productId = addToCartBtn.dataset.productId;
    const quantity = parseInt(quantityInput.value);
    
    if (<?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true ? 'true' : 'false'; ?>) {
        // Check if size is selected when sizes are available
        const sizeButtons = document.querySelectorAll('.size-btn');
        const activeSizeBtn = document.querySelector('.size-btn.active');
        
        if (sizeButtons.length > 0 && !activeSizeBtn) {
            showNotification('Please select a size first!');
            return;
        }
        
        let selectedSize = null;
        if (activeSizeBtn) {
            selectedSize = activeSizeBtn.dataset.size;
        }
        
        // User is logged in, add to cart then redirect to checkout
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}${selectedSize ? `&size=${selectedSize}` : ''}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/pages/checkout.php';
            } else {
                showNotification(data.message || 'Failed to process your order.');
            }
        })
        .catch(error => {
            console.error('Error processing order:', error);
            showNotification('An error occurred. Please try again.');
        });
    } else {
        // User is not logged in, show login popup
        popup('login-popup');
    }
});
                
                // Notification function
                window.showNotification = function(message) {
                    const notification = document.createElement('div');
                    notification.className = 'notification';
                    notification.innerHTML = message;
                    
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.classList.add('show');
                    }, 10);
                    
                    setTimeout(() => {
                        notification.classList.remove('show');
                        setTimeout(() => {
                            document.body.removeChild(notification);
                        }, 500);
                    }, 3000);
                };
                
                // Popup function
                window.popup = function(id) {
                    const popup = document.getElementById(id);
                    if (popup) {
                        popup.style.display = popup.style.display === 'flex' ? 'none' : 'flex';
                    }
                };
            });
        </script>
        
        <style>
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: #4CAF50;
                color: white;
                padding: 15px 20px;
                border-radius: 4px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                opacity: 0;
                transform: translateY(-20px);
                transition: opacity 0.3s, transform 0.3s;
                z-index: 1000;
            }
            .notification.show {
                opacity: 1;
                transform: translateY(0);
            }
            .size-btn.active {
                background-color: #088178;
                color: white;
            }
            .in-wishlist, .in-cart {
                background-color: #4CAF50 !important;
                color: white !important;
            }
        </style>
        <script src="/assets/js/script.js"></script>
    </body>
    </html>