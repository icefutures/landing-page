<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zero to Hero - Penghasilan Tambahan</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="/css/trade-theme.css">
</head>
<body>
    <main class="trade">
        <section class="trade-hero">
            <div class="trade-hero-media">
                <img src="/images/modalinv2.jpg" alt="Zero to Hero Banner">
            </div>
            <div class="trade-hero-content">
                <p class="trade-tag">Program Penghasilan Tambahan</p>
                <h1>Siap tambah penghasilan hingga 2 juta per hari?? Gabung sekarang dan wujudkan keuntungan nyata bersama kami!</h1>
                <p class="trade-highlight"><strong>Gunakan ponsel anda di rumah dan dapatkan penghasilan tambahan Rp 800ribu hingga 2 juta perhari</strong></p>
                <button class="trade-button" onclick="window.open('https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20tahu%20lebih%20lanjut%20tentang%20program%20ini', '_blank')">
                    <i class="fab fa-whatsapp"></i> Hubungi Kami Via WhatsApp Sekarang!
                </button>
            </div>
        </section>

        <section class="trade-section">
            <div class="trade-grid">
                <article class="trade-card">
                    <h3>Paket Basic</h3>
                    <p class="trade-price">Rp100.000</p>
                    <p>Modal awal yang terjangkau untuk mulai menghasilkan profit</p>
                    <button class="trade-outline" onclick="window.open('https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20Basic', '_blank')">Daftar Sekarang</button>
                </article>
                <article class="trade-card trade-card-highlight">
                    <h3>Paket Silver</h3>
                    <p class="trade-price">Rp500.000</p>
                    <p>Paket menengah dengan potensi profit lebih besar</p>
                    <button class="trade-button" onclick="window.open('https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20Silver', '_blank')">Daftar Sekarang</button>
                </article>
                <article class="trade-card">
                    <h3>Paket Gold</h3>
                    <p class="trade-price">Rp1.000.000</p>
                    <p>Investasi maksimal untuk hasil optimal</p>
                    <button class="trade-outline" onclick="window.open('https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20Gold', '_blank')">Daftar Sekarang</button>
                </article>
            </div>
        </section>

        <section class="trade-section trade-story">
            <div class="trade-story-card">
                <p>Dalam beberapa tahun terakhir, biaya hidup terus meningkat. Namun, ada solusi. Bergabunglah dengan kami dan temukan peluang mendapatkan penghasilan tambahan dari rumah.</p>
                <p>Kondisi ini membuat banyak dari kita merasa tertekan secara finansial. Namun, ada solusi. Bergabunglah dengan kami dan temukan peluang mendapatkan penghasilan tambahan dari rumah. Dengan bimbingan dari para profesional kami, Anda dapat meraih keuntungan tanpa risiko yang besar.</p>
                <p>Kami memberikan jaminan 100% uang kembali jika Anda tidak berhasil mendapatkan pendapatan atau mengalami kerugian. Jadi, jangan ragu lagi untuk memulai. Mari bersama-sama membangun masa depan finansial yang lebih stabil dan nyaman untuk Anda dan keluarga Anda.</p>
            </div>
        </section>

        <section class="trade-section">
            <div class="trade-quote">
                <strong><em>Saya telah membuktikan sendiri melalui platform ini saya telah menghasilkan penghasilan 23 juta / bulan, Anda bisa membuktikannya langsung</em></strong>
            </div>
        </section>

        <section class="trade-section">
            <div class="trade-carousel">
                <div class="mySlides">
                    <img src="/images/testimonial/testi1.jpg" alt="Testimonial 1" onclick="openModal(this.src)">
                </div>
                <div class="mySlides">
                    <img src="/images/testimonial/testi2.jpg" alt="Testimonial 2" onclick="openModal(this.src)">
                </div>
                <div class="mySlides">
                    <img src="/images/testimonial/testi3.jpg" alt="Testimonial 3" onclick="openModal(this.src)">
                </div>

                <div class="dots-container">
                    <span class="dot" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                    <span class="dot" onclick="currentSlide(3)"></span>
                </div>
            </div>
        </section>

        <section class="trade-section">
            <div class="trade-cta">
                <h2>Jangan lewatkan kesempatan emas ini! Bergabunglah sekarang dan raih kesuksesan finansial Anda!</h2>
                <button class="trade-button" onclick="window.open('https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20bergabung%20sekarang!', '_blank')">
                    <i class="fab fa-whatsapp"></i> Hubungi Kami Via WhatsApp Sekarang!
                </button>
            </div>
        </section>
    </main>

    <!-- Modal for Image Popup -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="img01">
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="links-logo-container">
            <div class="footer-logo">
                <img src="/images/modalinv2.jpg" alt="Logo">
            </div>
            
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
            </div>
            
            <div class="footer-links">
                <a href="/privacy-policy.html">Kebijakan Privasi</a>
                <a href="/terms.html">Syarat dan Ketentuan</a>
            </div>
        </div>
        
        <hr class="hr-style">
        
        <div class="footer-bottom">
            <p>&copy; 2026 Zero to Hero. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/js/slideshow.js"></script>
    <script src="/js/modal.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
