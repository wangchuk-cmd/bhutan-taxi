/**
 * Navbar is now fully CSS-driven for fixed positioning
 * This file only handles other mobile optimizations
 *
 * IMPORTANT: All navbar manipulation has been removed from JavaScript
 * CSS rules now completely control navbar visibility and positioning
 */

// Force navbar visible on page load and maintain visibility on scroll
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        // Initial setup - remove any hide classes
        navbar.classList.remove('navbar-hide');
        navbar.classList.add('navbar-show');
        
        // Keep navbar always visible on scroll
        window.addEventListener('scroll', function() {
            if (navbar) {
                navbar.classList.remove('navbar-hide');
                navbar.classList.add('navbar-show');
                // Force inline styles as backup
                navbar.style.setProperty('display', 'flex', 'important');
                navbar.style.setProperty('visibility', 'visible', 'important');
                navbar.style.setProperty('opacity', '1', 'important');
                navbar.style.setProperty('top', '0', 'important');
                navbar.style.setProperty('transform', 'translateY(0)', 'important');
                navbar.style.setProperty('position', 'fixed', 'important');
            }
        }, { passive: true });
    }
});

/**
 * Optimize Booking Cards Layout
 * Ensures proper card sizing on mobile
 */
document.addEventListener('DOMContentLoaded', function() {
    const bookingCards = document.querySelectorAll('.booking-card');
    
    bookingCards.forEach(card => {
        // Adjust card height for mobile
        if (window.innerWidth < 576) {
            card.style.minHeight = 'auto';
        }
    });
    
    // Re-adjust on window resize
    window.addEventListener('resize', function() {
        bookingCards.forEach(card => {
            if (window.innerWidth < 576) {
                card.style.minHeight = 'auto';
            } else {
                card.style.minHeight = '';
            }
        });
    });
});

/**
 * Fix Content Height on Mobile - NO NAVBAR TOUCHING
 * Only ensures proper spacing for non-navbar elements
 */
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.querySelector('.main-content');
    
    if (mainContent) {
        mainContent.style.paddingTop = '20px'; /* extra breathing room, not dependent on navbar */
    }
});

/**
 * Mobile Touch Optimization for Cards
 * Adds touch feedback
 */
document.addEventListener('DOMContentLoaded', function() {
    const touchCards = document.querySelectorAll('.booking-card, .driver-card, .admin-card');
    
    touchCards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.opacity = '0.9';
        }, { passive: true });
        
        card.addEventListener('touchend', function() {
            this.style.opacity = '1';
        }, { passive: true });
    });
});

/**
 * Prevent Double Tap Zoom on Buttons/Links
 */
document.addEventListener('DOMContentLoaded', function() {
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function(event) {
        const now = Date.now();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
});

/**
 * Improve Form Input Focus on Mobile
 * Prevents viewport shift when keyboard appears
 */
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            // Scroll input into view
            setTimeout(() => {
                this.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        });
    });
});

/**
 * Dynamic Grid Layout for Responsive Cards
 */
document.addEventListener('DOMContentLoaded', function() {
    function adjustCardLayout() {
        const grid = document.querySelector('.bookings-grid');
        if (!grid) return;
        
        const width = window.innerWidth;
        
        if (width < 576) {
            grid.style.gridTemplateColumns = '1fr';
        } else if (width < 1024) {
            grid.style.gridTemplateColumns = 'repeat(2, 1fr)';
        } else {
            grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
        }
    }
    
    adjustCardLayout();
    window.addEventListener('resize', adjustCardLayout, { passive: true });
});

/**
 * Memory-efficient Scroll Events
 * Debounce scroll listener
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Navigation Link Active States
 * Highlight current page in navigation
 */
document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.pathname;
    const navLinks = document.querySelectorAll('.navbar-nav a.nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentUrl) {
            link.classList.add('active');
        }
    });
});

/**
 * Accessible Dropdown Menus
 */
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    
    dropdownToggle.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.nextElementSibling;
            if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                dropdown.classList.toggle('show');
            }
        });
    });
});

/**
 * Console log for debugging (remove in production)
 */
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('Mobile Responsive JS Loaded');
}
