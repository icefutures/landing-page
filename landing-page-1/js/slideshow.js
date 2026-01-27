/**
 * Slideshow Module
 * Handles slideshow functionality including auto-slide and manual navigation
 */

let slideIndex = 1;
let autoSlideInterval;

/**
 * Initialize slideshow
 */
function initSlideshow() {
    showSlides(slideIndex);
    startAutoSlide();
}

/**
 * Change slides by n positions
 * @param {number} n - Number of positions to move
 */
function plusSlides(n) {
    clearInterval(autoSlideInterval);
    showSlides(slideIndex += n);
    startAutoSlide();
}

/**
 * Show specific slide
 * @param {number} n - Slide number to show
 */
function currentSlide(n) {
    clearInterval(autoSlideInterval);
    showSlides(slideIndex = n);
    startAutoSlide();
}

/**
 * Main slideshow function
 * @param {number} n - Slide number to display
 */
function showSlides(n) {
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    
    if (n > slides.length) { slideIndex = 1; }
    if (n < 1) { slideIndex = slides.length; }
    
    // Hide all slides
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    
    // Remove active class from all dots
    for (let i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    
    // Show current slide and activate corresponding dot
    slides[slideIndex - 1].style.display = "block";  
    dots[slideIndex - 1].className += " active";
}

/**
 * Start automatic slide transition
 */
function startAutoSlide() {
    autoSlideInterval = setInterval(() => {
        slideIndex++;
        showSlides(slideIndex);
    }, 4000); // Change slide every 4 seconds
}

// Export functions for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initSlideshow,
        plusSlides,
        currentSlide,
        showSlides,
        startAutoSlide
    };
}
