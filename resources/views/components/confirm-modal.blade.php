<!-- Modern Confirmation Modal Component -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content system-card-modal">
            <!-- Modal Header -->
            <div class="modal-header system-card-header">
                <div style="display: flex; align-items: center; gap: 12px; width: 100%;">
                    <div class="system-card-icon system-card-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title system-card-title">Confirm Action</h5>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body system-card-body">
                <p id="confirmMessage" class="system-card-message">Are you sure you want to proceed with this action?</p>

                <!-- Optional admin notes input -->
                <div id="confirmInputWrapper" class="mt-3" style="display:none;">
                    <label for="confirmAdminNotes" class="form-label fw-semibold text-dark">Admin notes (optional)</label>
                    <textarea id="confirmAdminNotes" rows="4" class="form-control system-card-textarea"></textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer system-card-footer">
                <button type="button" class="btn system-card-btn system-card-btn-cancel" id="confirmCancel">
                    Cancel
                </button>
                <button type="button" class="btn system-card-btn system-card-btn-confirm" id="confirmOK">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal Script -->
<script>
    let confirmCallback = null;

    function showConfirmModal(message, title = 'Confirm Action', onConfirm = null, options = {}) {
        // options: { showInput: bool, inputPlaceholder: string, inputDefault: string }
        confirmCallback = onConfirm;

        // Set modal title and message
        document.querySelector('.modal-title').textContent = title;
        document.getElementById('confirmMessage').textContent = message;

        // Configure optional input
        const inputWrapper = document.getElementById('confirmInputWrapper');
        const inputEl = document.getElementById('confirmAdminNotes');
        if (options.showInput) {
            inputWrapper.style.display = 'block';
            inputEl.placeholder = options.inputPlaceholder || '';
            inputEl.value = options.inputDefault || '';
            setTimeout(() => inputEl.focus(), 300);
        } else {
            inputWrapper.style.display = 'none';
            inputEl.value = '';
        }

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }

    // Handle confirm button
    document.getElementById('confirmOK').addEventListener('click', function() {
        let inputVal = '';
        const inputWrapper = document.getElementById('confirmInputWrapper');
        if (inputWrapper && inputWrapper.style.display !== 'none') {
            inputVal = document.getElementById('confirmAdminNotes').value || '';
        }

        if (confirmCallback && typeof confirmCallback === 'function') {
            try {
                confirmCallback(inputVal);
            } catch (e) {
                console.error('confirmCallback error', e);
            }
        }
        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
    });

    // Handle cancel button
    document.getElementById('confirmCancel').addEventListener('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
    });
</script>

<style>
    .system-card-modal {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }

    .system-card-header {
        border: none;
        padding: 22px 22px 14px 22px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.98));
    }

    .system-card-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .system-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .system-card-warning {
        background: #fef3c7;
        color: #d97706;
    }

    .system-card-body {
        padding: 18px 22px 8px 22px;
        color: #64748b;
        font-size: 15px;
        line-height: 1.6;
    }

    .system-card-message {
        margin: 0;
        color: #334155;
    }

    .system-card-textarea {
        border-radius: 14px;
        border-color: #dbe4f0;
        min-height: 110px;
        box-shadow: none;
    }

    .system-card-textarea:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.15);
    }

    .system-card-footer {
        border: none;
        padding: 14px 22px 22px 22px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.98));
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .system-card-btn {
        border: 0;
        padding: 10px 22px;
        border-radius: 12px;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .system-card-btn:hover {
        transform: translateY(-1px);
    }

    .system-card-btn-cancel {
        background: #e2e8f0;
        color: #334155;
    }

    .system-card-btn-cancel:hover {
        background: #cbd5e1;
    }

    .system-card-btn-confirm {
        background: #f59e0b;
        color: #fff;
    }

    .system-card-btn-confirm:hover {
        background: #d97706;
        box-shadow: 0 8px 18px rgba(245, 158, 11, 0.28);
    }

    .system-card-btn-confirm:focus,
    .system-card-btn-cancel:focus {
        box-shadow: none;
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
