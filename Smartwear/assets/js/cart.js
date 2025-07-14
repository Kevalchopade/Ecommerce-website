// cart.js - Create this new file

// Cart and wishlist data structures
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Save wishlist to localStorage
function saveWishlist() {
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
}

// Add product to cart
function addToCart(product, size, quantity = 1) {
    // Check if product already exists in cart with same size
    const existingItemIndex = cart.findIndex(item => 
        item.id === product.id && item.size === size
    );
    
    if (existingItemIndex >= 0) {
        // Update quantity if product already exists
        cart[existingItemIndex].quantity += quantity;
    } else {
        // Add new product to cart
        cart.push({
            id: product.id || product.prod_id,
            name: product.name,
            price: product.price,
            image: product.image1 || product.images,
            size: size,
            quantity: quantity,
            category: product.category,
            subcategory: product.subcategory || product["sub-category"]
        });
    }
    
    // Save cart to localStorage
    saveCart();
    
    // Show notification
    showNotification(`${product.name} added to cart`);
    
    // Update cart count in header
    updateCartCount();
}

// Add product to wishlist
function addToWishlist(product) {
    // Check if product already exists in wishlist
    const existingItemIndex = wishlist.findIndex(item => item.id === (product.id || product.prod_id));
    
    if (existingItemIndex >= 0) {
        // Remove from wishlist if already exists (toggle functionality)
        wishlist.splice(existingItemIndex, 1);
        showNotification(`${product.name} removed from wishlist`);
    } else {
        // Add new product to wishlist
        wishlist.push({
            id: product.id || product.prod_id,
            name: product.name,
            price: product.price,
            image: product.image1 || product.images,
            category: product.category,
            subcategory: product.subcategory || product["sub-category"]
        });
        showNotification(`${product.name} added to wishlist`);
    }
    
    // Save wishlist to localStorage
    saveWishlist();
    
    // Update wishlist count in header
    updateWishlistCount();
    
    // Update UI to reflect wishlist status
    updateWishlistButtons();
}

// Remove item from cart
function removeFromCart(id, size) {
    cart = cart.filter(item => !(item.id === id && item.size === size));
    saveCart();
    updateCartCount();
    
    // If on cart page, update cart display
    if (document.getElementById('cart-items')) {
        displayCartItems();
    }
}

// Remove item from wishlist
function removeFromWishlist(id) {
    wishlist = wishlist.filter(item => item.id !== id);
    saveWishlist();
    updateWishlistCount();
    
    // If on wishlist page, update wishlist display
    if (document.getElementById('wishlist-items')) {
        displayWishlistItems();
    }
    
    // Update UI to reflect wishlist status
    updateWishlistButtons();
}

// Update quantity of cart item
function updateCartItemQuantity(id, size, newQuantity) {
    const itemIndex = cart.findIndex(item => item.id === id && item.size === size);
    
    if (itemIndex >= 0) {
        if (newQuantity <= 0) {
            // Remove item if quantity is zero or negative
            removeFromCart(id, size);
        } else {
            // Update quantity
            cart[itemIndex].quantity = newQuantity;
            saveCart();
            
            // If on cart page, update cart display
            if (document.getElementById('cart-items')) {
                displayCartItems();
            }
        }
    }
}

// Check if product is in wishlist
function isInWishlist(productId) {
    return wishlist.some(item => item.id === productId);
}

// Show notification
function showNotification(message, duration = 3000) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('notification');
    
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        document.body.appendChild(notification);
    }
    
    // Set message and show notification
    notification.textContent = message;
    notification.classList.add('show');
    
    // Hide notification after duration
    setTimeout(() => {
        notification.classList.remove('show');
    }, duration);
}

// Update cart count in header
function updateCartCount() {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCountElement.textContent = totalItems;
        
        // Show/hide based on whether cart has items
        if (totalItems > 0) {
            cartCountElement.style.display = 'block';
        } else {
            cartCountElement.style.display = 'none';
        }
    }
}

// Update wishlist count in header
function updateWishlistCount() {
    const wishlistCountElement = document.getElementById('wishlist-count');
    if (wishlistCountElement) {
        wishlistCountElement.textContent = wishlist.length;
        
        // Show/hide based on whether wishlist has items
        if (wishlist.length > 0) {
            wishlistCountElement.style.display = 'block';
        } else {
            wishlistCountElement.style.display = 'none';
        }
    }
}

// Update wishlist buttons to reflect current state
function updateWishlistButtons() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        const productId = button.getAttribute('data-product-id');
        
        if (isInWishlist(productId)) {
            button.classList.add('in-wishlist');
            button.setAttribute('title', 'Remove from Wishlist');
        } else {
            button.classList.remove('in-wishlist');
            button.setAttribute('title', 'Add to Wishlist');
        }
    });
}

// Calculate cart total
function calculateCartTotal() {
    return cart.reduce((total, item) => {
        return total + (parseFloat(item.price) * item.quantity);
    }, 0).toFixed(2);
}

// Display cart items (for cart page)
function displayCartItems() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    
    if (!cartItemsContainer) return;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<div class="empty-cart">Your cart is empty</div>';
        if (cartTotalElement) cartTotalElement.textContent = '₹0.00';
        return;
    }
    
    // Clear previous items
    cartItemsContainer.innerHTML = '';
    
    // Add each cart item
    cart.forEach(item => {
        const cartItemElement = document.createElement('div');
        cartItemElement.className = 'cart-item';
        cartItemElement.innerHTML = `
            <div class="cart-item-image">
                <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="cart-item-details">
                <h3>${item.name}</h3>
                <p>Size: ${item.size}</p>
                <p>Price: ₹${item.price}</p>
                <div class="quantity-selector">
                    <span class="quantity-btn minus" data-id="${item.id}" data-size="${item.size}">-</span>
                    <input type="number" value="${item.quantity}" min="1" class="quantity-input" 
                           data-id="${item.id}" data-size="${item.size}">
                    <span class="quantity-btn plus" data-id="${item.id}" data-size="${item.size}">+</span>
                </div>
            </div>
            <div class="cart-item-price">
                ₹${(parseFloat(item.price) * item.quantity).toFixed(2)}
            </div>
            <button class="remove-btn" data-id="${item.id}" data-size="${item.size}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        `;
        
        cartItemsContainer.appendChild(cartItemElement);
    });
    
    // Add event listeners for quantity buttons and remove buttons
    document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            const item = cart.find(item => item.id === id && item.size === size);
            if (item) {
                updateCartItemQuantity(id, size, item.quantity - 1);
            }
        });
    });
    
    document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            const item = cart.find(item => item.id === id && item.size === size);
            if (item) {
                updateCartItemQuantity(id, size, item.quantity + 1);
            }
        });
    });
    
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            const newQuantity = parseInt(this.value, 10) || 1;
            updateCartItemQuantity(id, size, newQuantity);
        });
    });
    
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            removeFromCart(id, size);
        });
    });
    
    // Update cart total
    if (cartTotalElement) {
        cartTotalElement.textContent = `₹${calculateCartTotal()}`;
    }
}

// Display wishlist items (for wishlist page)
function displayWishlistItems() {
    const wishlistItemsContainer = document.getElementById('wishlist-items');
    
    if (!wishlistItemsContainer) return;
    
    if (wishlist.length === 0) {
        wishlistItemsContainer.innerHTML = '<div class="empty-wishlist">Your wishlist is empty</div>';
        return;
    }
    
    // Clear previous items
    wishlistItemsContainer.innerHTML = '';
    
    // Add each wishlist item
    wishlist.forEach(item => {
        const wishlistItemElement = document.createElement('div');
        wishlistItemElement.className = 'wishlist-item';
        wishlistItemElement.innerHTML = `
            <div class="wishlist-item-image">
                <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="wishlist-item-details">
                <h3>${item.name}</h3>
                <p>Price: ₹${item.price}</p>
                <button class="add-to-cart-btn small" data-product-id="${item.id}">Add to Cart</button>
            </div>
            <button class="remove-btn" data-id="${item.id}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        `;
        
        wishlistItemsContainer.appendChild(wishlistItemElement);
    });
    
    // Add event listeners for add to cart and remove buttons
    document.querySelectorAll('.wishlist-item .add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const product = wishlist.find(item => item.id === productId);
            
            if (product) {
                // Open a modal to select size if needed
                // For simplicity, we'll use a default size here
                const defaultSize = "M";
                addToCart(product, defaultSize);
            }
        });
    });
    
    document.querySelectorAll('.wishlist-item .remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            removeFromWishlist(id);
        });
    });
}

// Initialize cart and wishlist functionality
function initCartAndWishlist() {
    // Update counts on page load
    updateCartCount();
    updateWishlistCount();
    
    // Display items if on cart or wishlist page
    if (document.getElementById('cart-items')) {
        displayCartItems();
    }
    
    if (document.getElementById('wishlist-items')) {
        displayWishlistItems();
    }
    
    // Update wishlist buttons to reflect current state
    updateWishlistButtons();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initCartAndWishlist);

// Export functions for use in other files
window.cart = {
    addToCart,
    removeFromCart,
    updateCartItemQuantity,
    displayCartItems,
    getCart: () => cart,
    getTotal: calculateCartTotal
};

window.wishlist = {
    addToWishlist,
    removeFromWishlist,
    isInWishlist,
    displayWishlistItems,
    getWishlist: () => wishlist
};

// JavaScript for handling the quantity increment/decrement functionality

document.addEventListener('DOMContentLoaded', function() {
    // Find all quantity control elements
    const quantityControls = document.querySelectorAll('.quantity-control');
    
    quantityControls.forEach(control => {
      const decreaseBtn = control.querySelector('.decrease');
      const increaseBtn = control.querySelector('.increase');
      const input = control.querySelector('.quantity-input');
      
      // Add event listeners to the buttons
      decreaseBtn.addEventListener('click', function() {
        let value = parseInt(input.value);
        if (value > 1) {
          input.value = value - 1;
          // Trigger change event to update cart
          updateCartItemQuantity(control, input.value);
        }
      });
      
      increaseBtn.addEventListener('click', function() {
        let value = parseInt(input.value);
        if (value < 10) { // Set a reasonable maximum
          input.value = value + 1;
          // Trigger change event to update cart
          updateCartItemQuantity(control, input.value);
        }
      });
    });
    
    // Function to update cart when quantity changes
    function updateCartItemQuantity(control, newQuantity) {
      // Find the closest cart item parent
      const cartItem = control.closest('.cart-item');
      if (!cartItem) return;
      
      // Get product ID (you might store this in a data attribute)
      const productId = cartItem.getAttribute('data-product-id');
      if (!productId) return;
      
      // Update quantity in your cart data structure
      if (window.cart && typeof window.cart.updateQuantity === 'function') {
        window.cart.updateQuantity(productId, newQuantity);
      } else {
        console.log('Cart update function not available');
      }
    }
    
    // Event listeners for dropdown quantity selectors
    const quantitySelects = document.querySelectorAll('.quantity-select');
    
    quantitySelects.forEach(select => {
      select.addEventListener('change', function() {
        const cartItem = this.closest('.cart-item');
        if (!cartItem) return;
        
        const productId = cartItem.getAttribute('data-product-id');
        if (!productId) return;
        
        const newQuantity = this.value;
        
        // Update quantity in your cart
        if (window.cart && typeof window.cart.updateQuantity === 'function') {
          window.cart.updateQuantity(productId, newQuantity);
        } else {
          console.log('Cart update function not available');
        }
      });
    });
  });
  // Display cart items (for cart page)
function displayCartItems() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    
    if (!cartItemsContainer) return;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<div class="empty-cart">Your cart is empty</div>';
        if (cartTotalElement) cartTotalElement.textContent = '₹0.00';
        return;
    }
    
    // Clear previous items
    cartItemsContainer.innerHTML = '';
    
    // Add each cart item
    cart.forEach(item => {
        const cartItemElement = document.createElement('div');
        cartItemElement.className = 'cart-item';
        cartItemElement.innerHTML = `
            <div class="cart-item-image">
                <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="cart-item-details">
                <h3 class="cart-item-title">${item.name}</h3>
                <p>Size: ${item.size}</p>
                <p>Price: ₹${item.price}</p>
                <div class="quantity-control">
                    <button class="quantity-btn minus" data-id="${item.id}" data-size="${item.size}">-</button>
                    <input type="number" value="${item.quantity}" min="1" max="10" class="quantity-input" 
                           data-id="${item.id}" data-size="${item.size}">
                    <button class="quantity-btn plus" data-id="${item.id}" data-size="${item.size}">+</button>
                </div>
            </div>
            <div class="cart-item-price">
                ₹${(parseFloat(item.price) * item.quantity).toFixed(2)}
            </div>
            <button class="remove-btn" data-id="${item.id}" data-size="${item.size}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        `;
        
        cartItemsContainer.appendChild(cartItemElement);
    });
    
    // Add event listeners for quantity buttons and remove buttons
    document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            const item = cart.find(item => item.id === id && item.size === size);
            if (item && item.quantity > 1) {
                updateCartItemQuantity(id, size, item.quantity - 1);
            }
        });
    });
    
    document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            const item = cart.find(item => item.id === id && item.size === size);
            if (item && item.quantity < 10) {
                updateCartItemQuantity(id, size, item.quantity + 1);
            }
        });
    });
    
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            const newQuantity = parseInt(this.value, 10) || 1;
            
            // Ensure quantity is within allowed range
            const clampedQuantity = Math.max(1, Math.min(10, newQuantity));
            if (clampedQuantity !== newQuantity) {
                this.value = clampedQuantity;
            }
            
            updateCartItemQuantity(id, size, clampedQuantity);
        });
    });
    
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const size = this.getAttribute('data-size');
            removeFromCart(id, size);
        });
    });
    
    // Update cart total
    if (cartTotalElement) {
        cartTotalElement.textContent = `₹${calculateCartTotal()}`;
    }
}