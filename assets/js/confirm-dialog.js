/**
 * ConfirmDialog
 * Reusable confirmation dialog built on Bootstrap modal.
 */

class ConfirmDialog {
  /**
   * Show confirmation dialog.
   * @param {Object} options
   * @param {string} [options.title]
   * @param {string} [options.message]
   * @param {string} [options.confirmText]
   * @param {string} [options.cancelText]
   * @param {string} [options.confirmVariant] - Bootstrap btn-* variant (e.g. 'danger', 'primary')
   * @returns {Promise<boolean>} resolves true if confirmed, false otherwise
   */
  static confirm(options = {}) {
    return new Promise((resolve) => {
      const modalEl = document.getElementById('confirmDialog');
      if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        // Fallback to native confirm
        const ok = window.confirm(options.message || 'Are you sure?');
        resolve(ok);
        return;
      }

      const titleEl = document.getElementById('confirmDialogTitle');
      const messageEl = document.getElementById('confirmDialogMessage');
      const confirmBtn = document.getElementById('confirmDialogConfirm');
      const cancelBtn = document.getElementById('confirmDialogCancel');

      if (options.title && titleEl) titleEl.textContent = options.title;
      if (options.message && messageEl) messageEl.textContent = options.message;

      if (confirmBtn) {
        confirmBtn.textContent = options.confirmText || 'Confirm';
        // Reset and apply variant (default to primary for non-destructive actions)
        confirmBtn.className = 'btn';
        confirmBtn.classList.add(`btn-${options.confirmVariant || 'primary'}`);
      }
      if (cancelBtn) {
        cancelBtn.textContent = options.cancelText || 'Cancel';
      }

      const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);

      const cleanup = () => {
        confirmBtn && confirmBtn.removeEventListener('click', onConfirm);
        modalEl.removeEventListener('hidden.bs.modal', onHide);
      };

      const onConfirm = () => {
        cleanup();
        bsModal.hide();
        resolve(true);
      };

      const onHide = () => {
        cleanup();
        resolve(false);
      };

      confirmBtn && confirmBtn.addEventListener('click', onConfirm, { once: true });
      modalEl.addEventListener('hidden.bs.modal', onHide, { once: true });

      bsModal.show();
    });
  }
}

// Auto-bind elements with data-confirm attribute
// Example: <a href="..." data-confirm="Delete this user?">Delete</a>

document.addEventListener('click', (e) => {
  const target = e.target.closest('[data-confirm]');
  if (!target) return;

  // Avoid re-confirming an already confirmed action
  if (target.dataset.confirmResolved === 'true') return;

  e.preventDefault();

  const message = target.getAttribute('data-confirm') || 'Are you sure?';
  const title = target.getAttribute('data-confirm-title') || 'Please Confirm';
  const confirmText = target.getAttribute('data-confirm-ok') || 'Yes';
  const cancelText = target.getAttribute('data-confirm-cancel') || 'No';
  const variant = target.getAttribute('data-confirm-variant') || 'danger';

  ConfirmDialog.confirm({
    title,
    message,
    confirmText,
    cancelText,
    confirmVariant: variant,
  }).then((confirmed) => {
    if (!confirmed) return;

    target.dataset.confirmResolved = 'true';

    const tag = target.tagName.toLowerCase();
    if (tag === 'a' && target.getAttribute('href')) {
      window.location.href = target.getAttribute('href');
    } else if ((tag === 'button' || tag === 'input') && target.type === 'submit') {
      const form = target.form;
      if (form) form.submit();
    } else {
      target.click();
    }
  });
});

// Expose globally if needed
if (typeof window !== 'undefined') {
  window.ConfirmDialog = ConfirmDialog;
}

