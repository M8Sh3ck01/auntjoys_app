<?php
/**
 * Reusable confirmation dialog (Bootstrap modal)
 * Include this partial on pages that need confirm behavior.
 */
?>

<div class="modal fade" id="confirmDialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDialogTitle">Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmDialogMessage">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="confirmDialogCancel" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmDialogConfirm">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

