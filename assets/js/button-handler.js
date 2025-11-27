/**
 * Modern Button Handler
 * Provides unified button interactions, loading states, and feedback
 */

class ButtonHandler {
  constructor() {
    this.init();
  }

  /**
   * Initialize button handler
   */
  init() {
    // Add loading animation to form submission buttons
    this.handleFormSubmits();
    
    // Handle ripple effect on click
    this.handleRippleEffect();
    
    // Add accessibility features
    this.handleAccessibility();
  }

  /**
   * Handle form submissions with loading state
   */
  handleFormSubmits() {
    document.addEventListener('submit', (e) => {
      const form = e.target;
      const submitBtn = form.querySelector('button[type="submit"]');
      
      if (submitBtn && !submitBtn.hasAttribute('data-no-loading')) {
        this.setLoading(submitBtn, true);
        
        // Re-enable button if form validation fails
        setTimeout(() => {
          if (!form.reportValidity()) {
            this.setLoading(submitBtn, false);
          }
        }, 100);
      }
    });
  }

  /**
   * Set button loading state
   * @param {Element} button - Button element
   * @param {boolean} isLoading - Loading state
   * @param {string} loadingText - Optional text to show during loading
   */
  setLoading(button, isLoading, loadingText = null) {
    if (!button) return;

    if (isLoading) {
      // Store original content
      if (!button.dataset.originalContent) {
        button.dataset.originalContent = button.innerHTML;
      }
      
      // Add loading class and set text
      button.classList.add('is-loading');
      button.disabled = true;
      
      if (loadingText) {
        button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
      }
    } else {
      // Remove loading class and restore content
      button.classList.remove('is-loading');
      button.disabled = false;
      
      if (button.dataset.originalContent) {
        button.innerHTML = button.dataset.originalContent;
      }
    }
  }

  /**
   * Handle ripple effect on button click
   */
  handleRippleEffect() {
    const buttons = document.querySelectorAll('button, a.btn, input[type="submit"]');
    
    buttons.forEach(button => {
      button.addEventListener('click', (e) => {
        const rect = button.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const ripple = document.createElement('span');
        ripple.style.position = 'absolute';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.width = '0';
        ripple.style.height = '0';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.6)';
        ripple.style.pointerEvents = 'none';
        ripple.style.transform = 'translate(-50%, -50%)';
        
        // Add ripple animation
        ripple.animate([
          { width: '0px', height: '0px', opacity: 1 },
          { width: '300px', height: '300px', opacity: 0 }
        ], {
          duration: 600,
          easing: 'ease-out'
        });
        
        button.appendChild(ripple);
        
        // Remove ripple after animation
        setTimeout(() => ripple.remove(), 600);
      });
    });
  }

  /**
   * Add accessibility features to buttons
   */
  handleAccessibility() {
    const buttons = document.querySelectorAll('button, a.btn, input[type="submit"]');
    
    buttons.forEach(button => {
      // Add aria-label if missing and button has only icon
      if (button.innerHTML.trim().match(/^<i.*<\/i>$/)) {
        if (!button.getAttribute('aria-label') && !button.getAttribute('title')) {
          const icon = button.querySelector('i');
          if (icon) {
            const ariaLabel = icon.className
              .replace('fas', '')
              .replace('fa-', '')
              .replace(/-/g, ' ')
              .trim();
            button.setAttribute('aria-label', ariaLabel);
          }
        }
      }
      
      // Add keyboard support for custom buttons
      if (button.tagName === 'A' && button.classList.contains('btn')) {
        button.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            button.click();
          }
        });
      }
    });
  }

  /**
   * Show success feedback on button
   * @param {Element} button - Button element
   * @param {string} successText - Text to show
   * @param {number} duration - Duration in ms
   */
  showSuccess(button, successText = 'Success!', duration = 2000) {
    const originalContent = button.innerHTML;
    button.innerHTML = `<i class="fas fa-check"></i> ${successText}`;
    button.style.background = 'var(--color-success)';
    
    setTimeout(() => {
      button.innerHTML = originalContent;
      button.style.background = '';
    }, duration);
  }

  /**
   * Show error feedback on button
   * @param {Element} button - Button element
   * @param {string} errorText - Text to show
   * @param {number} duration - Duration in ms
   */
  showError(button, errorText = 'Error!', duration = 2000) {
    const originalContent = button.innerHTML;
    button.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorText}`;
    button.style.background = 'var(--color-danger)';
    
    setTimeout(() => {
      button.innerHTML = originalContent;
      button.style.background = '';
    }, duration);
  }

  /**
   * Disable all buttons in a container
   * @param {Element} container - Container element
   */
  disableAll(container = document) {
    const buttons = container.querySelectorAll('button, a.btn, input[type="submit"]');
    buttons.forEach(btn => btn.disabled = true);
  }

  /**
   * Enable all buttons in a container
   * @param {Element} container - Container element
   */
  enableAll(container = document) {
    const buttons = container.querySelectorAll('button, a.btn, input[type="submit"]');
    buttons.forEach(btn => btn.disabled = false);
  }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  window.buttonHandler = new ButtonHandler();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ButtonHandler;
}
