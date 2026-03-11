/**
 * Real-time Updates & Search Persistence
 * Handles localStorage for search params and polling for live updates
 */

const BhutanTaxi = {
    // Storage keys
    SEARCH_KEY: 'bhutan_taxi_search',
    
    // Save search params to localStorage
    saveSearch(from, to, date) {
        const search = { from, to, date, timestamp: Date.now() };
        localStorage.setItem(this.SEARCH_KEY, JSON.stringify(search));
    },
    
    // Get saved search params
    getSearch() {
        const data = localStorage.getItem(this.SEARCH_KEY);
        if (!data) return null;
        
        const search = JSON.parse(data);
        // Expire after 24 hours
        if (Date.now() - search.timestamp > 24 * 60 * 60 * 1000) {
            this.clearSearch();
            return null;
        }
        return search;
    },
    
    // Clear saved search
    clearSearch() {
        localStorage.removeItem(this.SEARCH_KEY);
    },

    // Get today's date in local timezone as YYYY-MM-DD
    getLocalTodayString() {
        const now = new Date();
        const tzOffsetMs = now.getTimezoneOffset() * 60000;
        return new Date(now.getTime() - tzOffsetMs).toISOString().slice(0, 10);
    },

    // Ensure date input behaves consistently across browsers/timezones
    normalizeDateInput(dateInput, applyDefault = false) {
        if (!dateInput) return;

        const localToday = this.getLocalTodayString();
        dateInput.min = localToday;

        if (applyDefault && !dateInput.value) {
            dateInput.value = localToday;
        }
    },
    
    // Initialize search form with saved values
    initSearchForm() {
        const search = this.getSearch();
        if (!search) return;
        
        // Fill in search fields if they exist and are empty
        const fromInput = document.querySelector('[name="from"], #search-from, #results-from');
        const toInput = document.querySelector('[name="to"], #search-to, #results-to');
        const dateInput = document.querySelector('[name="date"]');
        
        if (fromInput && !fromInput.value && search.from) {
            fromInput.value = search.from;
        }
        if (toInput && !toInput.value && search.to) {
            toInput.value = search.to;
        }
        if (dateInput && !dateInput.value && search.date) {
            // Only set date if it's today or in the future
            const searchDate = new Date(search.date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (searchDate >= today) {
                dateInput.value = search.date;
            }
        }
    },
    
    // Polling for real-time updates
    pollInterval: null,
    
    startPolling(callback, interval = 15000) {
        if (this.pollInterval) return;
        
        this.pollInterval = setInterval(() => {
            callback();
        }, interval);
        
        // Also poll immediately
        callback();
    },
    
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    },
    
    // Refresh search results via AJAX
    async refreshSearchResults() {
        const search = this.getSearch();
        if (!search) return;
        
        const tripsList = document.getElementById('trips-list');
        if (!tripsList) return;
        
        try {
            const response = await fetch(`/api/trips/search?from=${encodeURIComponent(search.from)}&to=${encodeURIComponent(search.to)}&date=${encodeURIComponent(search.date)}`);
            
            if (response.ok) {
                const data = await response.json();
                this.updateTripsList(data.trips, data.html);
            }
        } catch (error) {
            console.log('Polling error:', error);
        }
    },
    
    // Update trips list without full page reload
    updateTripsList(trips, html) {
        const tripsList = document.getElementById('trips-list');
        if (!tripsList || !html) return;
        
        // Only update if content changed
        const currentContent = tripsList.innerHTML.trim();
        const newContent = html.trim();
        
        if (currentContent !== newContent) {
            tripsList.innerHTML = html;
            
            // Show update notification
            this.showUpdateNotification();
        }
    },
    
    // Show subtle notification that content was updated
    showUpdateNotification() {
        // Remove existing notification
        const existing = document.getElementById('update-notification');
        if (existing) existing.remove();
        
        const notification = document.createElement('div');
        notification.id = 'update-notification';
        notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 70px; right: 20px; z-index: 9999; max-width: 300px; animation: slideIn 0.3s ease;';
        notification.innerHTML = `
            <i class="bi bi-arrow-repeat me-2"></i>
            <small>Results updated</small>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
};

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Save search on form submit
    const searchForms = document.querySelectorAll('form[action*="search"]');

    searchForms.forEach(form => {
        const dateInput = form.querySelector('[name="date"]');
        BhutanTaxi.normalizeDateInput(dateInput, true);

        form.addEventListener('submit', function() {
            const from = this.querySelector('[name="from"]')?.value;
            const to = this.querySelector('[name="to"]')?.value;
            const dateField = this.querySelector('[name="date"]');

            BhutanTaxi.normalizeDateInput(dateField, true);
            const date = dateField?.value;
            
            if (from && to && date) {
                BhutanTaxi.saveSearch(from, to, date);
            }
        });
    });
    
    // Initialize saved search on home page
    const isHomePage = window.location.pathname === '/' || window.location.pathname === '/home';
    if (isHomePage) {
        BhutanTaxi.initSearchForm();
    }
    
    // Start polling on search results page
    const tripsList = document.getElementById('trips-list');
    if (tripsList) {
        BhutanTaxi.startPolling(() => {
            BhutanTaxi.refreshSearchResults();
        }, 10000); // Poll every 10 seconds
    }
    
    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        BhutanTaxi.stopPolling();
    });
});

// Add CSS for notification animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);
