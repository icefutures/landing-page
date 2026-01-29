const sliderTrack = document.querySelector('[data-testimonial-track]');
const slides = sliderTrack ? Array.from(sliderTrack.children) : [];
const dots = document.querySelectorAll('[data-testimonial-dot]');
let activeIndex = 0;
let autoTimer;

function renderSlide(index) {
    if (!sliderTrack || slides.length === 0) return;
    const safeIndex = ((index % slides.length) + slides.length) % slides.length;
    activeIndex = safeIndex;
    sliderTrack.style.transform = `translateX(-${safeIndex * 100}%)`;
    dots.forEach((dot, dotIndex) => {
        dot.classList.toggle('active', dotIndex === safeIndex);
    });
}

function startAuto() {
    stopAuto();
    autoTimer = setInterval(() => {
        renderSlide(activeIndex + 1);
    }, 4500);
}

function stopAuto() {
    if (autoTimer) clearInterval(autoTimer);
}

dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        renderSlide(index);
        startAuto();
    });
});

if (sliderTrack) {
    sliderTrack.addEventListener('mouseenter', stopAuto);
    sliderTrack.addEventListener('mouseleave', startAuto);
}

renderSlide(0);
startAuto();

// FAQ toggle
const faqItems = document.querySelectorAll('[data-faq-item]');
faqItems.forEach((item) => {
    const question = item.querySelector('[data-faq-question]');
    question?.addEventListener('click', () => {
        faqItems.forEach((other) => {
            if (other !== item) other.classList.remove('active');
        });
        item.classList.toggle('active');
    });
});

// Modal
const modal = document.querySelector('[data-modal]');
const modalImg = document.querySelector('[data-modal-img]');
const modalClose = document.querySelector('[data-modal-close]');
const scrollTopButton = document.querySelector('[data-scroll-top]');

function openModal(src) {
    if (!modal || !modalImg || !src) return;
    modalImg.src = src;
    modal.style.display = 'flex';
}

function closeModal() {
    if (!modal) return;
    modal.style.display = 'none';
}

modalClose?.addEventListener('click', closeModal);
modal?.addEventListener('click', (event) => {
    if (event.target === modal) closeModal();
});

slides.forEach((slide) => {
    const img = slide.querySelector('img');
    img?.addEventListener('click', () => openModal(img.currentSrc || img.src));
});

// Scroll to top
if (scrollTopButton) {
    const toggleScrollButton = () => {
        scrollTopButton.classList.toggle('is-visible', window.scrollY > 300);
    };

    window.addEventListener('scroll', toggleScrollButton);
    toggleScrollButton();

    scrollTopButton.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
