<?php
include('../product/db.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../pages/home.php");
    exit;
}

// Process product update if form submitted
if (isset($_POST['update_product'])) {
    $prod_id = $_POST['prod_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    
    // Handle multiple sizes and format them correctly
    if (isset($_POST['sizes'])) {
        $selectedSizes = $_POST['sizes'];
        sort($selectedSizes); // Sort sizes in ascending order
        
        // Check if all sizes are selected
        $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $allSelected = (count(array_diff($allSizes, $selectedSizes)) === 0);
        
        if ($allSelected) {
            $size = 'S-XXL';
        } else {
            $size = implode(',', $selectedSizes);
        }
    } else {
        $size = '';
    }

    $updateQuery = "UPDATE products SET name=?, price=?, category=?, subcategory=?, brand=?, description=?, size=? WHERE prod_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sdsssssi", $name, $price, $category, $subcategory, $brand, $description, $size, $prod_id);
    $stmt->execute();
    
    // Redirect to refresh the page
    header("Location: product-management.php");
    exit();
}

// Process add product if form submitted
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    
    // Handle multiple sizes and format them correctly
    if (isset($_POST['sizes'])) {
        $selectedSizes = $_POST['sizes'];
        sort($selectedSizes); // Sort sizes in ascending order
        
        // Check if all sizes are selected
        $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $allSelected = (count(array_diff($allSizes, $selectedSizes)) === 0);
        
        if ($allSelected) {
            $size = 'S-XXL';
        } else {
            $size = implode(',', $selectedSizes);
        }
    } else {
        $size = '';
    }

    // File upload
    $img1 = $_FILES['image1']['name'];
    $tmp1 = $_FILES['image1']['tmp_name'];
    move_uploaded_file($tmp1, "../assets/uploads/$img1");

    $img2 = $_FILES['image2']['name'];
    $tmp2 = $_FILES['image2']['tmp_name'];
    if ($img2 != "") {
        move_uploaded_file($tmp2, "../assets/uploads/$img2");
    }

    $sql = "INSERT INTO products (name, price, category, subcategory, brand, image1, image2, description, size)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssssss", $name, $price, $category, $subcategory, $brand, $img1, $img2, $description, $size);
    $stmt->execute();
    
    // Redirect to refresh the page
    header("Location: product-management.php");
    exit();
}

// Fetch products
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product Management</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        /* Modal popup styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 50px auto;
            padding: 30px;
            width: 70%;
            max-width: 600px;
            border-radius: 5px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        /* Form styling */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }
        
        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .plus-add-btn {
  background: none;
  border: none;
  color: #008674; /* Your theme color */
  font-size: 32px;
  font-weight: bold;
  cursor: pointer;
  padding: 0;
  margin: 0;
  line-height: 1;
  transition: transform 0.2s, color 0.2s;
}

.plus-add-btn:hover {
  color: #006a59; /* Darker shade on hover */
  transform: scale(1.2);
}

.plus-add-btn:focus {
  outline: none;
}
        
        /* Table styling adjustments */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th, .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .admin-table th {
            background-color: #f2f2f2;
        }
        
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            margin: 2px;
            color: white;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
        
        .edit-btn {
            background-color: #007bff;
        }
        
        .delete-btn {
            background-color: #dc3545;
        }

        /* Select all sizes option */
        .all-sizes-option {
            border-top: 1px solid #ddd;
            margin-top: 10px;
            padding-top: 10px;
        }
        
        /* Page heading container */
        .page-heading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 20px;
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
                <li><a href="product-management.php" class="active">Product Management</a></li>
                <li><a href="order-management.php">Order Management</a></li>
                <li><a href="customer-management.php">Customer Management</a></li>
            </ul>
            <div class="admin-nav-right">
                <a href="/pages/profile.php" class="exit-btn">Exit</a>
            </div>
        </div>
    </section>

    <!-- Page Heading with Add Button -->
    <!-- Page Heading with Add Button -->
<div class="page-heading-container">
    <h2>Product Management</h2>
    <div class="spacer"></div>
    <button class="add-btn" onclick="openAddModal()">+</button>
</div>

    <!-- Product Table -->
    <div class="product-table-container" style="padding: 20px;">
        <table class="admin-table">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Sub-category</th>
                <th>Brand</th>
                <th>Description</th>
                <th>Size</th>
                <th>Actions</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><img src="/product/images/<?php echo $row['image1']; ?>" width="50"/></td>
                <td><?php echo $row['name']; ?></td>
                <td>₹<?php echo $row['price']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['subcategory']; ?></td>
                <td><?php echo $row['Brand']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo strtoupper($row['size']); ?></td>
                <td>
                    <button class="edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <a href="delete-product.php?id=<?php echo $row['prod_id']; ?>" 
                        class="delete-btn" 
                        onclick="return confirm('Are you sure you want to delete this product?');">
                        Delete
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Product</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editProductForm" method="POST" action="">
                <input type="hidden" name="prod_id" id="edit_prod_id">
                
                <div class="form-group">
                    <label for="edit_name">Name:</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_price">Price:</label>
                    <input type="number" step="0.01" id="edit_price" name="price" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Category:</label>
                    <input type="text" id="edit_category" name="category" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_subcategory">Sub-Category:</label>
                    <input type="text" id="edit_subcategory" name="subcategory" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_brand">Brand:</label>
                    <input type="text" id="edit_brand" name="brand" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description:</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Available Sizes:</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="edit_size_s" name="sizes[]" value="S">
                            <label for="edit_size_s">S</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="edit_size_m" name="sizes[]" value="M">
                            <label for="edit_size_m">M</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="edit_size_l" name="sizes[]" value="L">
                            <label for="edit_size_l">L</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="edit_size_xl" name="sizes[]" value="XL">
                            <label for="edit_size_xl">XL</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="edit_size_xxl" name="sizes[]" value="XXL">
                            <label for="edit_size_xxl">XXL</label>
                        </div>
                    </div>
                    <div class="all-sizes-option">
                        <div class="checkbox-item">
                            <input type="checkbox" id="edit_all_sizes" onchange="toggleAllSizes('edit')">
                            <label for="edit_all_sizes"><strong>All Sizes (S-XXL)</strong></label>
                        </div>
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Product</h3>
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="add_name">Name:</label>
                    <input type="text" id="add_name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="add_price">Price:</label>
                    <input type="number" step="0.01" id="add_price" name="price" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="add_category">Category:</label>
                    <input type="text" id="add_category" name="category" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="add_subcategory">Sub-Category:</label>
                    <input type="text" id="add_subcategory" name="subcategory" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="add_brand">Brand:</label>
                    <input type="text" id="add_brand" name="brand" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="add_image1">Main Image:</label>
                    <input type="file" id="add_image1" name="image1" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="add_image2">Second Image (Optional):</label>
                    <input type="file" id="add_image2" name="image2" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="add_description">Description:</label>
                    <textarea id="add_description" name="description" class="form-control" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Available Sizes:</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="add_size_s" name="sizes[]" value="S">
                            <label for="add_size_s">S</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="add_size_m" name="sizes[]" value="M">
                            <label for="add_size_m">M</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="add_size_l" name="sizes[]" value="L">
                            <label for="add_size_l">L</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="add_size_xl" name="sizes[]" value="XL">
                            <label for="add_size_xl">XL</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="add_size_xxl" name="sizes[]" value="XXL">
                            <label for="add_size_xxl">XXL</label>
                        </div>
                    </div>
                    <div class="all-sizes-option">
                        <div class="checkbox-item">
                            <input type="checkbox" id="add_all_sizes" onchange="toggleAllSizes('add')">
                            <label for="add_all_sizes"><strong>All Sizes (S-XXL)</strong></label>
                        </div>
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary">Add Product</button>
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

    <script>
        // Edit product modal functions
        function openEditModal(product) {
            document.getElementById('edit_prod_id').value = product.prod_id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_category').value = product.category;
            document.getElementById('edit_subcategory').value = product.subcategory;
            document.getElementById('edit_brand').value = product.Brand;
            document.getElementById('edit_description').value = product.description;
            
            // Reset checkboxes
            document.getElementById('edit_size_s').checked = false;
            document.getElementById('edit_size_m').checked = false;
            document.getElementById('edit_size_l').checked = false;
            document.getElementById('edit_size_xl').checked = false;
            document.getElementById('edit_size_xxl').checked = false;
            document.getElementById('edit_all_sizes').checked = false;
            
            // Check if it's a range (S-XXL)
            if (product.size === 'S-XXL') {
                document.getElementById('edit_all_sizes').checked = true;
                // Check all individual size checkboxes
                document.getElementById('edit_size_s').checked = true;
                document.getElementById('edit_size_m').checked = true;
                document.getElementById('edit_size_l').checked = true;
                document.getElementById('edit_size_xl').checked = true;
                document.getElementById('edit_size_xxl').checked = true;
            } else {
                // Set appropriate checkboxes based on product sizes
                const sizes = product.size ? product.size.toUpperCase().split(',') : [];
                sizes.forEach(size => {
                    const sizeCheckbox = document.getElementById('edit_size_' + size.toLowerCase());
                    if (sizeCheckbox) {
                        sizeCheckbox.checked = true;
                    }
                });
                
                // Check if all sizes are selected anyway
                const allSelected = 
                    document.getElementById('edit_size_s').checked &&
                    document.getElementById('edit_size_m').checked &&
                    document.getElementById('edit_size_l').checked &&
                    document.getElementById('edit_size_xl').checked &&
                    document.getElementById('edit_size_xxl').checked;
                
                document.getElementById('edit_all_sizes').checked = allSelected;
            }
            
            document.getElementById('editProductModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }
        
        // Add product modal functions
        function openAddModal() {
            document.getElementById('addProductModal').style.display = 'block';
        }
        
        function closeAddModal() {
            document.getElementById('addProductModal').style.display = 'none';
        }
        
        // Toggle all sizes
        function toggleAllSizes(prefix) {
            const allSizesCheckbox = document.getElementById(prefix + '_all_sizes');
            const sizeCheckboxes = [
                document.getElementById(prefix + '_size_s'),
                document.getElementById(prefix + '_size_m'),
                document.getElementById(prefix + '_size_l'),
                document.getElementById(prefix + '_size_xl'),
                document.getElementById(prefix + '_size_xxl')
            ];
            
            sizeCheckboxes.forEach(checkbox => {
                checkbox.checked = allSizesCheckbox.checked;
            });
        }
        
        // Update all sizes checkbox when individual sizes are clicked
        function updateAllSizesCheckbox(prefix) {
            const allSelected = 
                document.getElementById(prefix + '_size_s').checked &&
                document.getElementById(prefix + '_size_m').checked &&
                document.getElementById(prefix + '_size_l').checked &&
                document.getElementById(prefix + '_size_xl').checked &&
                document.getElementById(prefix + '_size_xxl').checked;
            
            document.getElementById(prefix + '_all_sizes').checked = allSelected;
        }
        
        // Add change event listeners to individual size checkboxes
        window.addEventListener('DOMContentLoaded', function() {
            ['edit', 'add'].forEach(prefix => {
                const sizeIds = ['s', 'm', 'l', 'xl', 'xxl'];
                sizeIds.forEach(size => {
                    const checkbox = document.getElementById(prefix + '_size_' + size);
                    if (checkbox) {
                        checkbox.addEventListener('change', function() {
                            updateAllSizesCheckbox(prefix);
                        });
                    }
                });
            });
        });
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editProductModal');
            const addModal = document.getElementById('addProductModal');
            
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
            
            if (event.target === addModal) {
                addModal.style.display = 'none';
            }
        }
    </script>
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/size_guide.js"></script>
</body>
</html>