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