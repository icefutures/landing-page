/**
 * Modal Module
 * Handles modal popup functionality for image viewing
 */

/**
 * Open modal with image
 * @param {HTMLImageElement} img - Image element to display in modal
 */
function openModal(img) {
    let modal = document.getElementById("myModal");
    let modalImg = document.getElementById("img01") || document.getElementById("imgModal");
    if (!modal || !modalImg) {
        return;
    }
    const src = typeof img === "string"
        ? img
        : (img?.currentSrc || img?.src || img?.target?.src);
    if (!src || src === "undefined") {
        return;
    }
    modal.style.display = "block";
    modalImg.src = src;
}

/**
 * Close modal
 */
function closeModal() {
    let modal = document.getElementById("myModal");
    if (!modal) {
        return;
    }
    modal.style.display = "none";
}

/**
 * Initialize modal event listeners
 */
function initModal() {
    // Close modal when clicking outside the image
    window.onclick = function(event) {
        let modal = document.getElementById("myModal");
        if (modal && event.target == modal) {
            modal.style.display = "none";
        }
    }
}

// Export functions for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        openModal,
        closeModal,
        initModal
    };
}
