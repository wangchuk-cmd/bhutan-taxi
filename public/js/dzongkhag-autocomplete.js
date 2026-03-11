/**
 * Dzongkhag Autocomplete Component
 * Provides typeahead functionality for origin/destination selection
 */

const DzongkhagLocations = [
    'Bumthang', 'Chhukha', 'Dagana', 'Gasa', 'Gelephu', 'Haa',
    'Lhuentse', 'Mongar', 'Paro', 'Pemagatshel', 'Phuentsholing', 'Punakha',
    'Samdrup Jongkhar', 'Samtse', 'Sarpang', 'Thimphu', 'Trashigang',
    'Trashiyangtse', 'Trongsa', 'Tsirang', 'Wangdue Phodrang', 'Zhemgang'
];

class DzongkhagAutocomplete {
    constructor(inputElement, options = {}) {
        this.input = typeof inputElement === 'string' ? document.querySelector(inputElement) : inputElement;
        if (!this.input) return;

        this.options = {
            locations: DzongkhagLocations,
            onSelect: options.onSelect || null,
            placeholder: options.placeholder || 'Type to search...',
            excludeValue: options.excludeValue || null, // Exclude another input's value
            excludeInput: options.excludeInput || null, // Reference to other input to exclude
            ...options
        };

        this.dropdown = null;
        this.selectedIndex = -1;
        this.isOpen = false;

        this.init();
    }

    init() {
        // Wrap input in container (for CSS hooks only, not for dropdown positioning)
        const wrapper = document.createElement('div');
        wrapper.className = 'dzongkhag-autocomplete-wrapper';
        this.input.parentNode.insertBefore(wrapper, this.input);
        wrapper.appendChild(this.input);

        // Append dropdown to body so overflow:hidden on cards never clips it
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'dzongkhag-dropdown';
        this.dropdown.style.cssText = 'display:none;position:fixed;z-index:99999;';
        document.body.appendChild(this.dropdown);

        // Add autocomplete attribute
        this.input.setAttribute('autocomplete', 'off');

        // Bind events
        this.bindEvents();
    }

    positionDropdown() {
        const rect = this.input.getBoundingClientRect();
        this.dropdown.style.top    = (rect.bottom + 1) + 'px';
        this.dropdown.style.left   = rect.left + 'px';
        this.dropdown.style.width  = rect.width + 'px';
    }

    bindEvents() {
        // Input events
        this.input.addEventListener('input', (e) => this.onInput(e));
        this.input.addEventListener('focus', (e) => this.onFocus(e));
        this.input.addEventListener('keydown', (e) => this.onKeydown(e));
        this.input.addEventListener('blur', (e) => {
            // Delay close to allow click events on dropdown items
            setTimeout(() => this.close(), 250);
        });

        // Dropdown mousedown (fires before blur)
        this.dropdown.addEventListener('mousedown', (e) => {
            e.preventDefault(); // Prevent blur
            const item = e.target.closest('.dzongkhag-item');
            if (item) {
                this.selectItem(item.dataset.value);
            }
        });
    }

    onInput(e) {
        const value = e.target.value.trim().toLowerCase();
        this.filter(value);
    }

    onFocus(e) {
        const value = e.target.value.trim().toLowerCase();
        this.filter(value);
    }

    onKeydown(e) {
        const items = this.dropdown.querySelectorAll('.dzongkhag-item');

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
                this.highlightItem(items);
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                this.highlightItem(items);
                break;
            case 'Enter':
                // Only prevent default if dropdown is open with selection
                if (this.isOpen && this.selectedIndex >= 0 && items[this.selectedIndex]) {
                    e.preventDefault();
                    this.selectItem(items[this.selectedIndex].dataset.value);
                }
                // Otherwise let form submit naturally
                break;
            case 'Escape':
                this.close();
                break;
        }
    }

    filter(query) {
        let locations = this.options.locations;

        // Exclude the other field's value if specified
        if (this.options.excludeInput) {
            const excludeEl = typeof this.options.excludeInput === 'string' 
                ? document.querySelector(this.options.excludeInput) 
                : this.options.excludeInput;
            if (excludeEl && excludeEl.value) {
                locations = locations.filter(loc => loc.toLowerCase() !== excludeEl.value.toLowerCase());
            }
        }

        // Filter by query
        const filtered = query 
            ? locations.filter(loc => loc.toLowerCase().startsWith(query) || loc.toLowerCase().includes(query))
            : locations;

        this.renderDropdown(filtered, query);
    }

    renderDropdown(locations, query = '') {
        if (locations.length === 0) {
            this.dropdown.innerHTML = '<div class="dzongkhag-no-results" style="padding: 12px; color: #6c757d; text-align: center;">No locations found</div>';
            this.open();
            return;
        }

        this.dropdown.innerHTML = locations.map((loc, index) => {
            const highlighted = query 
                ? loc.replace(new RegExp(`(${query})`, 'gi'), '<strong>$1</strong>')
                : loc;
            return `<div class="dzongkhag-item" data-value="${loc}" data-index="${index}">
                    <i class="bi bi-geo-alt text-primary me-2"></i>${highlighted}
                </div>`;
        }).join('');

        this.selectedIndex = -1;
        this.open();
    }

    highlightItem(items) {
        items.forEach((item, i) => {
            item.classList.toggle('active', i === this.selectedIndex);
        });
        if (items[this.selectedIndex]) {
            items[this.selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    selectItem(value) {
        this.input.value = value;
        this.close();

        // Trigger change event
        this.input.dispatchEvent(new Event('change', { bubbles: true }));

        // Callback
        if (this.options.onSelect) {
            this.options.onSelect(value, this.input);
        }

        // Focus next input if specified
        if (this.options.nextInput) {
            const next = typeof this.options.nextInput === 'string'
                ? document.querySelector(this.options.nextInput)
                : this.options.nextInput;
            if (next) {
                setTimeout(() => next.focus(), 50);
            }
        }
    }

    open() {
        this.positionDropdown();
        this.dropdown.style.display = 'block';
        this.isOpen = true;
    }

    close() {
        this.dropdown.style.display = 'none';
        this.isOpen = false;
        this.selectedIndex = -1;
    }
}

// jQuery plugin wrapper
if (typeof jQuery !== 'undefined') {
    jQuery.fn.dzongkhagAutocomplete = function(options) {
        return this.each(function() {
            if (!this._dzongkhagAutocomplete) {
                this._dzongkhagAutocomplete = new DzongkhagAutocomplete(this, options);
            }
        });
    };
}

// Auto-initialize on elements with data-dzongkhag-autocomplete
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-dzongkhag-autocomplete]').forEach(input => {
        const excludeInput = input.dataset.excludeInput || null;
        const nextInput = input.dataset.nextInput || null;
        new DzongkhagAutocomplete(input, { excludeInput, nextInput });
    });
});
