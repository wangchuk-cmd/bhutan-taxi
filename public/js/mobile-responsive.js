/**
 * Smart Navbar Hide/Show on Scroll
 * Auto-hides navbar when scrolling down, shows when scrolling up
 */

document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    
    if (!navbar) return;
    
    let lastScrollTop = 0;
    let isScrollingDown = false;
    let scrollTimeout;
    const scrollThreshold = 10; // Minimum scroll distance before triggering hide/show
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        
        // Only apply auto-hide on mobile/tablet (less than 992px)
        if (window.innerWidth >= 992) {
            navbar.classList.remove('navbar-hide');
            navbar.classList.add('navbar-show');
            return;
        }
        
        const scrollDiff = Math.abs(currentScroll - lastScrollTop);
        
        // Check if scroll difference is significant enough
        if (scrollDiff < scrollThreshold) {
            return;
        }
        
        // Scrolling down
        if (currentScroll > lastScrollTop) {
            if (!isScrollingDown) {
                navbar.classList.add('navbar-hide');
                navbar.classList.remove('navbar-show');
                isScrollingDown = true;
            }
        } 
        // Scrolling up
        else {
            if (isScrollingDown) {
                navbar.classList.remove('navbar-hide');
                navbar.classList.add('navbar-show');
                isScrollingDown = false;
            }
        }
        
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    }, { passive: true });
    
    // Show navbar on top of page
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        if (currentScroll <= 0) {
            navbar.classList.remove('navbar-hide');
            navbar.classList.add('navbar-show');
            isScrollingDown = false;
        }
    }, { passive: true });
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
 * Fix Content Height on Mobile
 * Ensures content doesn't overlap with fixed navbar on scroll
 */
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    const mainContent = document.querySelector('main') || document.querySelector('[role="main"]');
    
    if (navbar && mainContent) {
        function updateMargin() {
            const navbarHeight = navbar.offsetHeight;
            if (window.innerWidth < 992) {
                mainContent.style.paddingTop = navbarHeight + 'px';
            } else {
                mainContent.style.paddingTop = '0';
            }
        }
        
        updateMargin();
        window.addEventListener('resize', updateMargin, { passive: true });
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
