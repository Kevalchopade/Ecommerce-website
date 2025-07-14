// checkout.js - JavaScript for the checkout page

document.addEventListener('DOMContentLoaded', function() {
    // Display checkout items and calculate totals
    displayCheckoutItems();
    
    // Handle payment method selection
    const paymentRadios = document.querySelectorAll('input[name="payment-method"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentInfo);
    });
    
    // Form submission
    const checkoutForm = document.getElementById('place-order-btn');
    checkoutForm.addEventListener('click', handleCheckoutSubmission);
    
    // Apply coupon (placeholder for now)
    const applyCouponBtn = document.querySelector('.apply-coupon-btn');
    applyCouponBtn.addEventListener('click', function() {
        const couponInput = document.querySelector('.coupon-input');
        const couponCode = couponInput.value.trim();
        
        if (couponCode) {
            // Placeholder for coupon validation
            showNotification('Coupon applied: ' + couponCode);
            updateOrderSummary();
        } else {
            showNotification('Please enter a coupon code');
        }
    });
});

// Display items in checkout summary
function displayCheckoutItems() {
    const checkoutItemsContainer = document.getElementById('checkout-items');
    const cart = window.cart.getCart();
    
    if (!checkoutItemsContainer) return;
    
    if (cart.length === 0) {
        checkoutItemsContainer.innerHTML = '<p>Your cart is empty</p>';
        updateOrderSummary();
        return;
    }
    
    // Clear previous items
    checkoutItemsContainer.innerHTML = '';
    
    // Add each cart item to the checkout summary
    cart.forEach(item => {
        const checkoutItemElement = document.createElement('div');
        checkoutItemElement.className = 'checkout-item';
        checkoutItemElement.innerHTML = `
            <div class="checkout-item-image">
                <img src="${item.image}" alt="${item.name}">
                <div class="checkout-item-quantity">${item.quantity}</div>
            </div>
            <div class="checkout-item-details">
                <div class="checkout-item-title">${item.name}</div>
                <div class="checkout-item-variant">Size: ${item.size}</div>
            </div>
            <div class="checkout-item-price">₹${(parseFloat(item.price) * item.quantity).toFixed(2)}</div>
        `;
        
        checkoutItemsContainer.appendChild(checkoutItemElement);
    });
    
    // Update order summary values
    updateOrderSummary();
}

// Toggle display of payment method information
function togglePaymentInfo() {
    const selectedMethod = document.querySelector('input[name="payment-method"]:checked').value;
    
    // Hide all payment info sections
    document.querySelectorAll('.payment-info').forEach(info => {
        info.style.display = 'none';
    });
    
    // Show the selected payment method's info section
    if (selectedMethod === 'upi') {
        document.getElementById('upi-info').style.display = 'block';
    } else if (selectedMethod === 'card') {
        document.getElementById('card-info').style.display = 'block';
    }
}

// Update order summary calculations
function updateOrderSummary() {
    const subtotal = parseFloat(window.cart.getTotal());
    const shipping = subtotal > 0 ? 50 : 0; // Free shipping for orders over a certain amount can be added here
    
    // Calculate tax (e.g., 5% of subtotal)
    const taxRate = 0.05;
    const tax = subtotal * taxRate;
    
    // Calculate total
    const total = subtotal + shipping + tax;
    
    // Update summary elements
    document.getElementById('summary-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('summary-shipping').textContent = `₹${shipping.toFixed(2)}`;
    document.getElementById('summary-tax').textContent = `₹${tax.toFixed(2)}`;
    document.getElementById('summary-total').textContent = `₹${total.toFixed(2)}`;
}

// Handle form submission
function handleCheckoutSubmission(event) {
    event.preventDefault();
    
    // Check if cart is empty
    if (window.cart.getCart().length === 0) {
        showNotification('Your cart is empty');
        return;
    }
    
    // Validate form fields
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const address = document.getElementById('address').value;
    const city = document.getElementById('city').value;
    const state = document.getElementById('state').value;
    const zipcode = document.getElementById('zipcode').value;
    const termsAccepted = document.getElementById('terms').checked;
    
    // Simple validation - check required fields
    if (!email || !phone || !firstName || !lastName || !address || !city || !state || !zipcode) {
        showNotification('Please fill in all required fields');
        return;
    }
    
    if (!termsAccepted) {
        showNotification('Please accept the terms and conditions');
        return;
    }
    
    // Get selected payment method
    const paymentMethod = document.querySelector('input[name="payment-method"]:checked').value;
    
    // Additional validation for specific payment methods
    if (paymentMethod === 'upi') {
        const upiId = document.querySelector('#upi-info input').value;
        if (!upiId) {
            showNotification('Please enter your UPI ID');
            return;
        }
    } else if (paymentMethod === 'card') {
        const cardNumber = document.getElementById('card-number').value;
        const cardExpiry = document.getElementById('card-expiry').value;
        const cardCvv = document.getElementById('card-cvv').value;
        
        if (!cardNumber || !cardExpiry || !cardCvv) {
            showNotification('Please enter all card details');
            return;
        }
    }
    
    // If all validation passes, process the order
    processOrder({
        customer: {
            email,
            phone,
            firstName,
            lastName,
            address,
            city,
            state,
            zipcode
        },
        payment: {
            method: paymentMethod
        },
        items: window.cart.getCart(),
        totals: {
            subtotal: parseFloat(window.cart.getTotal()),
            shipping: 50, // Should match the value in updateOrderSummary
            tax: parseFloat(window.cart.getTotal()) * 0.05 // Should match the calculation in updateOrderSummary
        }
    });
}

// Process the order (in a real app, this would send data to a server)
function processOrder(orderData) {
    // This is a placeholder for the actual order processing
    console.log('Processing order:', orderData);
    
    // Show confirmation message
    showNotification('Order placed successfully!');
    
    // Clear the cart
    localStorage.removeItem('cart');
    
    // Redirect to an order confirmation page (would be implemented in a real app)
    setTimeout(() => {
        alert('Order placed successfully! Order ID: ORD' + Math.floor(Math.random() * 1000000));
        window.location.href = 'index.html'; // Redirect to home page
    }, 1500);
}

// Notification helper 
function showNotification(message, duration = 3000) {
    // Create notification element if it doesn't exist
    let notification = document.getElementById('notification');
    
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.left = '50%';
        notification.style.transform = 'translateX(-50%)';
        notification.style.backgroundColor = '#4CAF50';
        notification.style.color = 'white';
        notification.style.padding = '8px 15px';
        notification.style.borderRadius = '4px';
        notification.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        notification.style.transition = 'opacity 0.3s';
        notification.style.opacity = '0';
        notification.style.zIndex = '1000';
        notification.style.maxWidth = '300px'; // Limit the width
        notification.style.textAlign = 'center';
        notification.style.maxHeight = '30px'
        notification.style.fontSize = '14px'; // Smaller text
        document.body.appendChild(notification);
    }
    
    // Set message and show notification
    notification.textContent = message;
    notification.style.opacity = '1';
    
    // Hide notification after duration
    setTimeout(() => {
        notification.style.opacity = '0';
    }, duration);
}