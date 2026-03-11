/**
 * Input Validation with Popup Feedback
 * Shows red popup tooltip + shake animation when user enters invalid characters
 */

document.addEventListener('DOMContentLoaded', function() {
    initPhoneValidation();
    initNameValidation();
    
    // Use MutationObserver for dynamically added fields
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                setTimeout(() => {
                    initPhoneValidation();
                    initNameValidation();
                }, 50);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Phone Number Validation - Numbers only
function initPhoneValidation() {
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name*="phone"], input[inputmode="numeric"]');
    
    phoneInputs.forEach(input => {
        if (input.dataset.phoneValidated) return;
        input.dataset.phoneValidated = 'true';
        
        // Remove any inline oninput handlers
        input.removeAttribute('oninput');
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('pattern', '[0-9]+');
        
        const tooltip = createTooltip(input, 'Numbers only');
        
        // Block non-numeric keys using keydown (fires before keypress)
        input.addEventListener('keydown', function(e) {
            // Allow control keys
            if (['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'].includes(e.key)) {
                return;
            }
            // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            if (e.ctrlKey || e.metaKey) {
                return;
            }
            // Block non-numeric
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
                showValidationError(input, tooltip, 'Numbers only');
            }
        });
        
        // Handle paste - clean non-numeric
        input.addEventListener('paste', function(e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^\d+$/.test(pastedText)) {
                e.preventDefault();
                const cleanedText = pastedText.replace(/[^0-9]/g, '');
                if (cleanedText) {
                    document.execCommand('insertText', false, cleanedText);
                }
                showValidationError(input, tooltip, 'Numbers only');
            }
        });
        
        // Cleanup on input (backup)
        input.addEventListener('input', function() {
            const cleaned = this.value.replace(/[^0-9]/g, '');
            if (cleaned !== this.value) {
                this.value = cleaned;
                showValidationError(input, tooltip, 'Numbers only');
            }
        });
    });
}

// Name Validation - Letters and spaces only (no numbers)
function initNameValidation() {
    const nameInputs = document.querySelectorAll('input[name="name"], input[name*="[name]"], input[name="passenger_name"]');
    
    nameInputs.forEach(input => {
        if (input.dataset.nameValidated) return;
        input.dataset.nameValidated = 'true';
        
        const tooltip = createTooltip(input, 'Letters only');
        
        // Block numbers using keydown
        input.addEventListener('keydown', function(e) {
            // Allow control keys
            if (['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'].includes(e.key)) {
                return;
            }
            // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            if (e.ctrlKey || e.metaKey) {
                return;
            }
            // Block numbers
            if (/[0-9]/.test(e.key)) {
                e.preventDefault();
                showValidationError(input, tooltip, 'Letters only');
            }
        });
        
        // Handle paste - remove numbers
        input.addEventListener('paste', function(e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (/[0-9]/.test(pastedText)) {
                e.preventDefault();
                const cleanedText = pastedText.replace(/[0-9]/g, '');
                if (cleanedText) {
                    document.execCommand('insertText', false, cleanedText);
                }
                showValidationError(input, tooltip, 'Letters only');
            }
        });
        
        // Cleanup on input (backup)
        input.addEventListener('input', function() {
            const cleaned = this.value.replace(/[0-9]/g, '');
            if (cleaned !== this.value) {
                this.value = cleaned;
                showValidationError(input, tooltip, 'Letters only');
            }
        });
    });
}

// Create tooltip reference (not used anymore, but kept for compatibility)
function createTooltip(input, defaultText) {
    return { message: defaultText };
}

// Show validation error with shake + message inside input
let hideTimeouts = new Map();
let originalPlaceholders = new Map();

function showValidationError(input, tooltip, message) {
    // Save original placeholder if not already saved
    if (!originalPlaceholders.has(input)) {
        originalPlaceholders.set(input, input.placeholder || '');
    }
    
    // Show message inside input as placeholder
    const currentValue = input.value;
    input.value = '';
    input.placeholder = message;
    input.classList.add('validation-error-placeholder');
    
    // Red shake animation on input
    input.classList.add('shake-error');
    input.style.borderColor = '#dc3545';
    input.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
    
    setTimeout(() => {
        input.classList.remove('shake-error');
    }, 400);
    
    // Restore after delay
    if (hideTimeouts.has(input)) {
        clearTimeout(hideTimeouts.get(input));
    }
    hideTimeouts.set(input, setTimeout(() => {
        input.value = currentValue;
        input.placeholder = originalPlaceholders.get(input);
        input.classList.remove('validation-error-placeholder');
        input.style.borderColor = '';
        input.style.boxShadow = '';
    }, 1000));
}

// Add shake animation and placeholder styling CSS
const validationStyle = document.createElement('style');
validationStyle.textContent = `
    @keyframes shake-error {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-6px); }
        40% { transform: translateX(6px); }
        60% { transform: translateX(-4px); }
        80% { transform: translateX(4px); }
    }
    .shake-error {
        animation: shake-error 0.4s ease !important;
    }
    
    /* Red placeholder text when showing error */
    .validation-error-placeholder::placeholder {
        color: #dc3545 !important;
        opacity: 1 !important;
        font-weight: 500;
    }
`;
document.head.appendChild(validationStyle);

// Global function to reinitialize validation (can be called manually)
window.reinitInputValidation = function() {
    initPhoneValidation();
    initNameValidation();
};
