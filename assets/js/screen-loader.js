/**
 * Screen Loader
 * Simple, modern full-screen loading spinner controller
 */

class ScreenLoader {
  static show() {
    const el = document.getElementById('screenLoader');
    if (el) {
      el.classList.remove('d-none');
    }
  }

  static hide() {
    const el = document.getElementById('screenLoader');
    if (el) {
      el.classList.add('d-none');
    }
  }
}

// Convenience global functions for use in inline scripts
function showScreenLoader() {
  ScreenLoader.show();
}

function hideScreenLoader() {
  ScreenLoader.hide();
}

// Optional: expose for other scripts if using modules/bundlers
if (typeof window !== 'undefined') {
  window.ScreenLoader = ScreenLoader;
}

// Auto-show loader for forms that opt in via data-screen-loader="true"
document.addEventListener('submit', (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;

  if (form.getAttribute('data-screen-loader') === 'true') {
    showScreenLoader();
  }
});
