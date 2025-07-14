
    document.addEventListener("DOMContentLoaded", function() {
    // Elements for search overlay
    const searchButton = document.getElementById("search-button");
    const searchOverlay = document.getElementById("search-overlay");
    const searchCloseBtn = document.getElementById("search-close-btn");
    const searchInput = document.getElementById("search-input-overlay");
    const searchSubmit = document.getElementById("search-submit");
    const wishlistAction = document.getElementById("wishlist-action");
    const cartAction = document.getElementById("cart-action");
    
    // Profile dropdown functionality
    const profileBtn = document.getElementById("profile-btn");
    const profileMenu = document.getElementById("profile-menu");
    
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener("click", function(event) {
            event.stopPropagation();
            profileMenu.classList.toggle("show");
        });
        
        document.addEventListener("click", function(event) {
            if (!profileBtn.contains(event.target) && !profileMenu.contains(event.target)) {
                profileMenu.classList.remove("show");
            }
        });
    }
    
    // Open search overlay
    if (searchButton && searchOverlay) {
        searchButton.addEventListener("click", function() {
            searchOverlay.classList.add("active");
            setTimeout(() => {
                if (searchInput) searchInput.focus();
            }, 400);
        });
    }
    
    // Close search overlay
    if (searchCloseBtn) {
        searchCloseBtn.addEventListener("click", function() {
            searchOverlay.classList.remove("active");
        });
    }
    
    // Submit search
    if (searchSubmit && searchInput) {
        searchSubmit.addEventListener("click", function() {
            submitSearch();
        });
        
        searchInput.addEventListener("keyup", function(e) {
            if (e.key === "Enter") {
                submitSearch();
            }
        });
    }
    
    function submitSearch() {
        if (searchInput && searchInput.value.trim() !== "") {
            window.location.href = 'search.html?q=' + encodeURIComponent(searchInput.value);
        }
    }
    
    // Action buttons functionality
    if (wishlistAction) {
        wishlistAction.addEventListener("click", function() {
            window.location.href = '/pages/wishlist.html';
        });
    }
    
    if (cartAction) {
        cartAction.addEventListener("click", function() {
            window.location.href = '/pages/cart.html';
        });
    }
    
    // Close search overlay when clicking Escape key
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && searchOverlay && searchOverlay.classList.contains("active")) {
            searchOverlay.classList.remove("active");
        }
    });
    
    // Login popup functionality
    const loginLinks = document.querySelectorAll('a[href="/pages/login.html"], #profile-menu a[href$="/pages/login.html"]');
    loginLinks.forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault();
            popup('login-popup');
        });
    });
    
    // Create account link
    const createAccountLink = document.querySelector(".create-account");
    if (createAccountLink) {
        createAccountLink.addEventListener("click", function(event) {
            event.preventDefault();
            openRegisterPopup();
        });
    }
    
    // Forgot password link
    const forgotPasswordLink = document.querySelector(".forgot-password");
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener("click", function(event) {
            event.preventDefault();
            openForgotPasswordPopup();
        });
    }
    
    // Next button in forgot password
    const nextButton = document.getElementById("nextButton");
    if (nextButton) {
        nextButton.addEventListener("click", function(event) {
            event.preventDefault();
            const emailInput = document.querySelector("#forgotPasswordContainer input[type='email']");
            if (emailInput && emailInput.value.trim()) {
                openResetPasswordPopup();
            }
        });
    }
    
    // Mobile menu toggle
    const bar = document.getElementById("bar");
    const nav = document.getElementById("navbar");
    const close = document.getElementById("close");
    
    if (bar) {
        bar.addEventListener("click", function() {
            nav.classList.add("active");
        });
    }
    
    if (close) {
        close.addEventListener("click", function() {
            nav.classList.remove("active");
        });
    }
});

// Popup functions
function popup(popup_name) {
    let get_popup = document.getElementById(popup_name);
    if (get_popup) {
        if (get_popup.style.display === "flex") {
            get_popup.style.display = "none";
        } else {
            get_popup.style.display = "flex";
        }
    }
}

function openRegisterPopup() {
    const registerPopup = document.getElementById("registerPopup");
    if (registerPopup) {
        registerPopup.style.display = "block";
    }
}

function closeRegisterPopup() {
    const registerPopup = document.getElementById("registerPopup");
    if (registerPopup) {
        registerPopup.style.display = "none";
    }
}

function openForgotPasswordPopup() {
    const loginPopup = document.getElementById("login-popup");
    const forgotPasswordContainer = document.getElementById("forgotPasswordContainer");
    
    if (loginPopup) {
        loginPopup.style.display = "none";
    }
    
    if (forgotPasswordContainer) {
        forgotPasswordContainer.style.display = "flex";
        const emailInput = forgotPasswordContainer.querySelector("input[type='email']");
        if (emailInput) {
            emailInput.value = "";
        }
    }
}

function closeForgotPasswordPopup() {
    const forgotPasswordContainer = document.getElementById("forgotPasswordContainer");
    if (forgotPasswordContainer) {
        forgotPasswordContainer.style.display = "none";
        const emailInput = forgotPasswordContainer.querySelector("input[type='email']");
        if (emailInput) {
            emailInput.value = "";
        }
    }
}

function openResetPasswordPopup() {
    const forgotPasswordContainer = document.getElementById("forgotPasswordContainer");
    const resetPasswordContainer = document.getElementById("resetPasswordContainer");
    
    if (forgotPasswordContainer) {
        forgotPasswordContainer.style.display = "none";
    }
    
    if (resetPasswordContainer) {
        resetPasswordContainer.style.display = "block";
        const newPasswordInput = document.getElementById("newPassword");
        const confirmPasswordInput = document.getElementById("confirmPassword");
        
        if (newPasswordInput) newPasswordInput.value = "";
        if (confirmPasswordInput) confirmPasswordInput.value = "";
    }
}

function closeResetPasswordPopup() {
    const resetPasswordContainer = document.getElementById("resetPasswordContainer");
    if (resetPasswordContainer) {
        resetPasswordContainer.style.display = "none";
        const newPasswordInput = document.getElementById("newPassword");
        const confirmPasswordInput = document.getElementById("confirmPassword");
        
        if (newPasswordInput) newPasswordInput.value = "";
        if (confirmPasswordInput) confirmPasswordInput.value = "";
    }
}

function submitNewPassword() {
    const newPasswordInput = document.getElementById("newPassword");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    
    if (newPasswordInput && confirmPasswordInput) {
        if (newPasswordInput.value !== confirmPasswordInput.value) {
            alert("Passwords don't match!");
            return;
        }
        
        if (newPasswordInput.value.trim() === "") {
            alert("Password cannot be empty!");
            return;
        }
        
        // Here you would typically submit the new password to your server
        alert("Password has been reset successfully!");
        closeResetPasswordPopup();
    }
}
// Reset Password handling
function openForgotPasswordPopup() {
    document.getElementById('forgotPasswordContainer').style.display = 'flex';
    // Hide any previous messages
    document.getElementById('emailVerificationMessage').textContent = '';
}

function closeForgotPasswordPopup() {
    document.getElementById('forgotPasswordContainer').style.display = 'none';
}

function openResetPasswordPopup() {
    // Make sure elements exist before trying to access them
    const resetPopup = document.getElementById('resetPasswordPopup');
    const forgotPopup = document.getElementById('forgotPasswordContainer');
    
    if (resetPopup) {
        resetPopup.style.display = 'flex';
    } else {
        console.error("Reset password popup element not found!");
    }
    
    if (forgotPopup) {
        forgotPopup.style.display = 'none';
    }
    
    console.log("Reset password popup should be visible now");
}

function closeResetPasswordPopup() {
    const resetPopup = document.getElementById('resetPasswordPopup');
    if (resetPopup) {
        resetPopup.style.display = 'none';
    }
}

// Handle verify email form submission
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded");
    
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        console.log("Found forgot password form");
        
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log("Forgot password form submitted");
            
            const email = this.querySelector('input[name="email"]').value;
            const messageDiv = document.getElementById('emailVerificationMessage');
            
            // Send verification request
            fetch('../product/verify_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                console.log("Verification response:", data);
                
                if (data.success) {
                    messageDiv.textContent = data.message;
                    messageDiv.style.color = 'green';
                    
                    // Show reset password popup after a short delay
                    console.log("Will show reset password popup in 1 second");
                    setTimeout(() => {
                        console.log("Executing openResetPasswordPopup now");
                        openResetPasswordPopup();
                    }, 1000);
                } else {
                    messageDiv.textContent = data.message;
                    messageDiv.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'An error occurred. Please try again.';
                messageDiv.style.color = 'red';
            });
        });
    } else {
        console.error("Forgot password form not found!");
    }
    
    // Check if reset password form exists
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    if (resetPasswordForm) {
        console.log("Found reset password form");
        
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log("Reset password form submitted");
            
            const newPassword = this.querySelector('input[name="new_password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            const messageDiv = document.querySelector('.reset-message');
            
            // Client-side validation
            if (newPassword !== confirmPassword) {
                messageDiv.textContent = 'Passwords do not match!';
                messageDiv.style.color = 'red';
                return;
            }
            
            if (newPassword.length < 6) {
                messageDiv.textContent = 'Password must be at least 6 characters long.';
                messageDiv.style.color = 'red';
                return;
            }
            
            // Send reset request
            fetch('../product/reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'new_password=' + encodeURIComponent(newPassword) + 
                      '&confirm_password=' + encodeURIComponent(confirmPassword)
            })
            .then(response => response.json())
            .then(data => {
                console.log("Reset response:", data);
                
                if (data.success) {
                    messageDiv.textContent = data.message;
                    messageDiv.style.color = 'green';
                    // Close popup and show login after successful reset
                    setTimeout(() => {
                        closeResetPasswordPopup();
                        popup('login-popup');
                    }, 2000);
                } else {
                    messageDiv.textContent = data.message;
                    messageDiv.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'An error occurred. Please try again.';
                messageDiv.style.color = 'red';
            });
        });
    } else {
        console.error("Reset password form not found!");
    }
    
    // Debug check for popup elements
    console.log("Forgot password container exists:", !!document.getElementById('forgotPasswordContainer'));
    console.log("Reset password popup exists:", !!document.getElementById('resetPasswordPopup'));
});
