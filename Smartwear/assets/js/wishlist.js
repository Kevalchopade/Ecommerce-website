// Wishlist functionality
window.wishlist = (function() {
    // Get wishlist from localStorage or initialize empty array
    function getWishlist() {
      const wishlist = localStorage.getItem('wishlist');
      return wishlist ? JSON.parse(wishlist) : [];
    }
    
    // Save wishlist to localStorage
    function saveWishlist(wishlist) {
      localStorage.setItem('wishlist', JSON.stringify(wishlist));
    }
    
    // Add item to wishlist
    function addToWishlist(product) {
      const wishlist = getWishlist();
      
      // Check if product already exists in wishlist
      const existingProductIndex = wishlist.findIndex(item => item.id === product.id);
      
      if (existingProductIndex === -1) {
        wishlist.push(product);
        saveWishlist(wishlist);
        showNotification('Product added to wishlist');
        return true;
      } else {
        showNotification('Product already in wishlist');
        return false;
      }
    }
    
    // Remove item from wishlist
    function removeFromWishlist(productId) {
      const wishlist = getWishlist();
      const updatedWishlist = wishlist.filter(item => item.id !== productId);
      
      saveWishlist(updatedWishlist);
      showNotification('Product removed from wishlist');
      
      // Refresh wishlist display
      displayWishlistItems();
      
      // Show/hide empty wishlist message
      const emptyWishlistElement = document.getElementById('wishlist-empty');
      if (updatedWishlist.length === 0 && emptyWishlistElement) {
        emptyWishlistElement.style.display = 'block';
      }
      
      return true;
    }
    
    // Display wishlist items
    function displayWishlistItems() {
      const wishlistItemsContainer = document.getElementById('wishlist-items');
      if (!wishlistItemsContainer) return;
      
      const wishlist = getWishlist();
      
      // Clear container
      wishlistItemsContainer.innerHTML = '';
      
      if (wishlist.length === 0) {
        // If we have the empty wishlist element, show it
        const emptyWishlistElement = document.getElementById('wishlist-empty');
        if (emptyWishlistElement) {
          emptyWishlistElement.style.display = 'block';
        }
        return;
      }
      
      // Hide empty wishlist message if it exists
      const emptyWishlistElement = document.getElementById('wishlist-empty');
      if (emptyWishlistElement) {
        emptyWishlistElement.style.display = 'none';
      }
      
      // Create and append wishlist items
      wishlist.forEach(product => {
        const wishlistItem = document.createElement('div');
        wishlistItem.classList.add('wishlist-item');
        
        wishlistItem.innerHTML = `
          <div class="wishlist-item-image">
            <img src="${product.image}" alt="${product.name}">
            <div class="wishlist-item-hover">
              <button class="quick-view-btn" data-product-id="${product.id}">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                </svg>
              </button>
            </div>
          </div>
          <div class="wishlist-item-details">
            <h3 class="wishlist-item-title">${product.name}</h3>
            <div class="wishlist-item-price">₹${parseFloat(product.price).toLocaleString()}</div>
            <div class="wishlist-item-actions">
              <button class="add-to-cart-btn" data-product-id="${product.id}">Add to Cart</button>
              <button class="remove-from-wishlist" data-product-id="${product.id}">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
                Remove
              </button>
            </div>
          </div>
        `;
        
        wishlistItemsContainer.appendChild(wishlistItem);
      });
      
      // Add event listeners to buttons
      const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
      addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productId = this.getAttribute('data-product-id');
          const product = wishlist.find(item => item.id === productId);
          
          if (product && window.cart && typeof window.cart.addToCart === 'function') {
            window.cart.addToCart(product);
          } else {
            showNotification('Error adding product to cart');
          }
        });
      });
      
      const removeButtons = document.querySelectorAll('.remove-from-wishlist');
      removeButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productId = this.getAttribute('data-product-id');
          removeFromWishlist(productId);
        });
      });
      
      const quickViewButtons = document.querySelectorAll('.quick-view-btn');
      quickViewButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productId = this.getAttribute('data-product-id');
          // Implement quick view functionality
          // For example, redirect to product page
          window.location.href = `product.html?id=${productId}`;
        });
      });
    }
    
    // Show notification
    function showNotification(message) {
      // Check if there's an existing notification element
      let notification = document.querySelector('.notification');
      
      // If not, create one
      if (!notification) {
        notification = document.createElement('div');
        notification.classList.add('notification');
        document.body.appendChild(notification);
      }
      
      // Set message and show notification
      notification.textContent = message;
      notification.classList.add('show');
      
      // Hide notification after 3 seconds
      setTimeout(() => {
        notification.classList.remove('show');
      }, 3000);
    }
    
    // Return public methods
    return {
      getWishlist,
      addToWishlist,
      removeFromWishlist,
      displayWishlistItems
    };
  })();