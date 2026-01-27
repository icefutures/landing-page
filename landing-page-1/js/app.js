/**
 * Main Application Entry Point
 * Initialize all modules when DOM is ready
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize slideshow
    if (typeof initSlideshow === 'function') {
        initSlideshow();
    }
    
    // Initialize modal
    if (typeof initModal === 'function') {
        initModal();
    }
    
    console.log('Landing page initialized successfully');
});

/**
 * Handle package button clicks
 * @param {string} url - URL to redirect to
 */
function handlePackageClick(url) {
    window.location.href = url;
}
