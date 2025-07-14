// Add this to your script.js file
document.addEventListener('DOMContentLoaded', function() {
    // Get all input, select, and textarea elements
    const formInputs = document.querySelectorAll('input:not([type="checkbox"]), select, textarea');
    
    // Add event listeners to each form element
    formInputs.forEach(input => {
      // Check if the input has a value on page load
      if (input.value.trim() !== '') {
        input.parentElement.classList.add('active');
      }
      
      // Focus event
      input.addEventListener('focus', function() {
        this.parentElement.classList.add('active', 'focused');
      });
      
      // Blur event
      input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
        if (this.value.trim() === '') {
          this.parentElement.classList.remove('active');
        }
      });
    });
    
    // Form validation
    const contactForm = document.querySelector('#form');
    if (contactForm) {
      contactForm.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = contactForm.querySelectorAll('input[required], select[required], textarea[required]');
        
        requiredFields.forEach(field => {
          if (field.value.trim() === '') {
            field.parentElement.classList.add('error');
            isValid = false;
          } else {
            field.parentElement.classList.remove('error');
          }
        });
        
        if (!isValid) {
          e.preventDefault();
        }
      });
    }
  });