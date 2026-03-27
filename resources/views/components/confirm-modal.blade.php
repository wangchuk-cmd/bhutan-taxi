<!-- Modern Confirmation Modal Component -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
            <!-- Modal Header -->
            <div class="modal-header" style="border: none; padding: 24px 24px 16px 24px; background: #ffffff;">
                <div style="display: flex; align-items: center; gap: 12px; width: 100%;">
                    <div style="width: 44px; height: 44px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 24px; color: #f59e0b;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">Confirm Action</h5>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="padding: 24px; color: #6b7280; font-size: 15px; line-height: 1.6;">
                <p id="confirmMessage" style="margin: 0;">Are you sure you want to proceed with this action?</p>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer" style="border: none; padding: 16px 24px 24px 24px; background: #ffffff; display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn" id="confirmCancel" style="background: #e5e7eb; color: #374151; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                    Cancel
                </button>
                <button type="button" class="btn" id="confirmOK" style="background: #f59e0b; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal Script -->
<script>
    let confirmCallback = null;

    function showConfirmModal(message, title = 'Confirm Action', onConfirm = null) {
        confirmCallback = onConfirm;
        
        // Set modal title and message
        document.querySelector('.modal-title').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }

    // Handle confirm button
    document.getElementById('confirmOK').addEventListener('click', function() {
        if (confirmCallback && typeof confirmCallback === 'function') {
            confirmCallback();
        }
        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
    });

    // Handle cancel button
    document.getElementById('confirmCancel').addEventListener('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
    });
</script>

<style>
    #confirmOK:hover {
        background: #f59e0b !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        transform: translateY(-2px);
    }

    #confirmCancel:hover {
        background: #d1d5db !important;
    }

    .modal-content {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
