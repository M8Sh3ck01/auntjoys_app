/**
 * Skeleton Loader Manager
 * Handles showing/hiding skeleton loaders during async operations
 */

class SkeletonLoader {
  /**
   * Show skeleton loaders while content loads
   * @param {number} count - Number of skeleton cards to show
   * @param {string} selector - Container selector where skeletons will be inserted
   */
  static showSkeletons(count = 12, selector = '.meals-container') {
    const container = document.querySelector(selector);
    if (!container) return;

    let skeletonHTML = '';
    for (let i = 0; i < count; i++) {
      skeletonHTML += `
        <div class="col-md-4 mb-4">
          <div class="card h-100 skeleton-card">
            <div class="skeleton skeleton-image"></div>
            <div class="card-body d-flex flex-column">
              <div class="skeleton skeleton-title mb-2"></div>
              <div class="skeleton skeleton-text"></div>
              <div class="skeleton skeleton-text mb-3"></div>
              <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="skeleton skeleton-price"></div>
                  <div class="skeleton skeleton-badge"></div>
                </div>
                <div class="skeleton skeleton-button"></div>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    container.innerHTML = `<div class="row">${skeletonHTML}</div>`;
  }

  /**
   * Replace skeleton with actual content
   * @param {string} selector - Container selector
   * @param {string} htmlContent - New HTML content to insert
   * @param {number} delay - Optional delay before replacement (ms)
   */
  static replaceWithContent(selector, htmlContent, delay = 0) {
    const container = document.querySelector(selector);
    if (!container) return;

    if (delay > 0) {
      setTimeout(() => {
        container.innerHTML = htmlContent;
      }, delay);
    } else {
      container.innerHTML = htmlContent;
    }
  }

  /**
   * Fade out skeleton and fade in content
   * @param {string} skeletonSelector - Skeleton container selector
   * @param {string} contentHTML - New HTML content
   * @param {number} duration - Animation duration (ms)
   */
  static fadeTransition(skeletonSelector, contentHTML, duration = 300) {
    const skeleton = document.querySelector(skeletonSelector);
    if (!skeleton) return;

    // Fade out skeleton
    skeleton.classList.add('fade-out-skeleton');

    setTimeout(() => {
      skeleton.innerHTML = contentHTML;
      skeleton.classList.remove('fade-out-skeleton');
    }, duration);
  }

  /**
   * Show loading skeleton for single card
   * @param {string} cardSelector - Card container selector
   */
  static showSingleSkeleton(cardSelector) {
    const card = document.querySelector(cardSelector);
    if (!card) return;

    const skeletonHTML = `
      <div class="skeleton-card">
        <div class="skeleton skeleton-image"></div>
        <div class="card-body d-flex flex-column">
          <div class="skeleton skeleton-title mb-2"></div>
          <div class="skeleton skeleton-text"></div>
          <div class="skeleton skeleton-text mb-3"></div>
          <div class="mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="skeleton skeleton-price"></div>
              <div class="skeleton skeleton-badge"></div>
            </div>
            <div class="skeleton skeleton-button"></div>
          </div>
        </div>
      </div>
    `;

    card.innerHTML = skeletonHTML;
  }

  /**
   * Hide all skeletons
   * @param {string} selector - Container selector
   */
  static hideSkeletons(selector = '.skeleton-card') {
    const skeletons = document.querySelectorAll(selector);
    skeletons.forEach((skeleton) => {
      skeleton.style.display = 'none';
    });
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  // Auto-hide skeletons if they exist (for pre-rendered pages)
  const hasSkeletons = document.querySelector('.skeleton');
  if (hasSkeletons) {
    // Skeletons will auto-hide when page fully loads
    window.addEventListener('load', () => {
      document.querySelectorAll('.skeleton').forEach((el) => {
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.3s ease';
      });
    });
  }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
  module.exports = SkeletonLoader;
}
