<?php
/**
 * About Us Page
 * Student Name: hanniba
 * Student ID: hanniba
 *
 * This page displays information about the company
 */

session_start();

// Set variables for header
$pageTitle = 'About Us';
$additionalStyles = '<style>
        /* Hero Section - White to Gray Gradient */
        .about-hero {
            background: linear-gradient(135deg, #f5f5f7 0%, #d2d2d7 100%);
            color: #1d1d1f;
            padding: 120px 20px 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        #about-particles-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .about-hero h1 {
            font-size: 64px;
            font-weight: 700;
            margin: 0 0 24px 0;
            color: #1d1d1f;
            letter-spacing: -2px;
            line-height: 1.1;
        }

        .about-hero p {
            font-size: 24px;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.5;
            color: #424245;
            font-weight: 400;
        }

        .container {
            max-width: 1232px;
            margin: 0 auto;
            padding: 80px 20px;
            background-color: #fbfbfd;
        }

        .about-section {
            background: #ffffff;
            border-radius: 18px;
            padding: 64px;
            margin-bottom: 48px;
            border: 1px solid #d2d2d7;
        }

        .about-section h2 {
            font-size: 40px;
            font-weight: 700;
            color: #1d1d1f;
            margin: 0 0 48px 0;
            letter-spacing: -1px;
            text-align: center;
        }

        .about-content {
            font-size: 17px;
            line-height: 1.8;
            color: #1d1d1f;
            max-width: 980px;
            margin: 0 auto;
        }

        .about-content p {
            margin-bottom: 24px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin: 56px auto 0 auto;
            max-width: 1232px;
        }

        .feature-card {
            background: #f5f5f7;
            padding: 40px 32px;
            border-radius: 18px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #d2d2d7;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            font-size: 56px;
            margin-bottom: 24px;
            filter: grayscale(20%);
        }

        .feature-card h3 {
            font-size: 21px;
            font-weight: 600;
            color: #1d1d1f;
            margin: 0 0 16px 0;
            letter-spacing: -0.3px;
        }

        .feature-card p {
            font-size: 15px;
            color: #86868b;
            line-height: 1.6;
            margin: 0;
        }

        .map-section {
            background: #ffffff;
            border-radius: 18px;
            padding: 64px;
            border: 1px solid #d2d2d7;
        }

        .map-section h2 {
            font-size: 40px;
            font-weight: 700;
            color: #1d1d1f;
            margin: 0 0 48px 0;
            letter-spacing: -1px;
            text-align: center;
        }

        .map-intro {
            max-width: 980px;
            margin: 0 auto 48px auto;
            text-align: center;
        }

        .map-intro p {
            font-size: 17px;
            line-height: 1.8;
            color: #1d1d1f;
        }

        .map-container {
            width: 100%;
            height: 500px;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #d2d2d7;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin: 48px auto;
            max-width: 1232px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 24px;
            background: #f5f5f7;
            border-radius: 18px;
            border: 1px solid #d2d2d7;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .contact-icon {
            width: 24px;
            height: 24px;
            color: #0071e3;
        }

        .contact-details h4 {
            font-size: 17px;
            font-weight: 600;
            color: #1d1d1f;
            margin: 0 0 8px 0;
            letter-spacing: -0.2px;
        }

        .contact-details p {
            font-size: 15px;
            color: #86868b;
            margin: 0;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .about-hero {
                padding: 80px 20px 60px;
            }

            .about-hero h1 {
                font-size: 40px;
            }

            .about-hero p {
                font-size: 19px;
            }

            .about-section {
                padding: 40px 24px;
            }

            .about-section h2 {
                font-size: 32px;
            }

            .map-section {
                padding: 40px 24px;
            }

            .map-section h2 {
                font-size: 32px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .contact-info {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .map-container {
                height: 350px;
            }

            .about-content {
                font-size: 15px;
            }
        }
    </style>';

// Include header
include 'includes/header.php';
?>

    <div class="about-hero">
        <canvas id="about-particles-canvas"></canvas>
        <div class="hero-content">
            <h1>About Hanniba Store</h1>
            <p>Your trusted partner for high-quality graphics cards and computer hardware since 2020</p>
        </div>
    </div>

    <div class="container">
        <div class="about-section">
            <h2>Our Story</h2>
            <div class="about-content">
                <p>
                    Hanniba Store was founded in 2020 with a simple mission: to provide enthusiasts and professionals
                    with the best graphics cards and computer hardware at competitive prices. What started as a small
                    operation has grown into a trusted destination for PC builders and gamers worldwide.
                </p>
                <p>
                    We understand that choosing the right graphics card is crucial for your computing experience,
                    whether you're a gamer, content creator, or professional. That's why we carefully curate our
                    selection to include only the best products from leading manufacturers like NVIDIA and AMD.
                </p>
                <p>
                    Our commitment to customer satisfaction goes beyond just selling products. We provide expert
                    advice, detailed product information, and exceptional after-sales support to ensure you get
                    the most out of your purchase.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Fast Shipping</h3>
                    <p>We ship orders quickly and securely, ensuring your products arrive safely and on time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h3>Quality Guaranteed</h3>
                    <p>All our products are 100% authentic and come with manufacturer warranties.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Expert Support</h3>
                    <p>Our knowledgeable team is here to help you choose the right products for your needs.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Best Prices</h3>
                    <p>We offer competitive pricing and regular discounts to give you the best value.</p>
                </div>
            </div>
        </div>

        <div class="map-section">
            <h2>Visit Our Store</h2>
            <div class="about-content map-intro">
                <p>
                    We welcome you to visit our physical store where you can see our products in person
                    and speak with our expert team. Located in the heart of the tech district, our store
                    is easily accessible by public transportation and offers convenient parking.
                </p>
            </div>

            <div class="contact-info">
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <div class="contact-details">
                        <h4>Address</h4>
                        <p>100 Smart Street<br>Los Angeles, CA, USA</p>
                    </div>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <div class="contact-details">
                        <h4>Phone</h4>
                        <p>1234567890</p>
                    </div>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <div class="contact-details">
                        <h4>Email</h4>
                        <p>company@email.com</p>
                    </div>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12,6 12,12 16,14"></polyline>
                    </svg>
                    <div class="contact-details">
                        <h4>Business Hours</h4>
                        <p>Mon-Fri: 9:00 AM - 6:00 PM<br>Sat: 10:00 AM - 4:00 PM<br>Sun: Closed</p>
                    </div>
                </div>
            </div>

            <div class="map-container" style="margin-top: 40px;">
                <iframe
                    src="https://map.baidu.com/search/%E7%BE%8E%E5%9B%BD%E6%B4%9B%E6%9D%89%E7%9F%B6/@-13128526.66,4016762.55,12z?querytype=s&da_src=shareurl&wd=%E7%BE%8E%E5%9B%BD%E6%B4%9B%E6%9D%89%E7%9F%B6&c=1&src=0&pn=0&sug=0&l=12&b=(-13146350.665,3999962.12;-13110702.655,4033562.98)&from=webmap&biz_forward=%7B%22scaler%22:1,%22styles%22:%22pl%22%7D&device_ratio=1"
                    width="100%"
                    height="450"
                    frameborder="0"
                    scrolling="no"
                    allowfullscreen="true">
                </iframe>
            </div>
        </div>
    </div>

    <script>
        // Particle Effect for About Hero Section
        (function() {
            const canvas = document.getElementById('about-particles-canvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            const particles = [];
            const particleCount = 60;
            const mouse = { x: null, y: null, radius: 150 };

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 3 + 1;
                    this.speedX = Math.random() * 1 - 0.5;
                    this.speedY = Math.random() * 1 - 0.5;
                    this.opacity = Math.random() * 0.5 + 0.3;
                }

                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
                    if (this.y > canvas.height || this.y < 0) this.speedY *= -1;

                    if (mouse.x != null && mouse.y != null) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < mouse.radius) {
                            const force = (mouse.radius - distance) / mouse.radius;
                            const dirX = dx / distance;
                            const dirY = dy / distance;
                            this.x -= dirX * force * 2.5;
                            this.y -= dirY * force * 2.5;
                        }
                    }
                }

                draw() {
                    const gradient = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, this.size);
                    gradient.addColorStop(0, `rgba(0, 113, 227, ${this.opacity})`);
                    gradient.addColorStop(0.5, `rgba(134, 134, 139, ${this.opacity * 0.7})`);
                    gradient.addColorStop(1, `rgba(134, 134, 139, 0)`);

                    ctx.fillStyle = gradient;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            function init() {
                particles.length = 0;
                for (let i = 0; i < particleCount; i++) {
                    particles.push(new Particle());
                }
            }

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(particle => {
                    particle.update();
                    particle.draw();
                });
                connect();
                requestAnimationFrame(animate);
            }

            function connect() {
                for (let a = 0; a < particles.length; a++) {
                    for (let b = a + 1; b < particles.length; b++) {
                        const dx = particles[a].x - particles[b].x;
                        const dy = particles[a].y - particles[b].y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < 100) {
                            ctx.strokeStyle = `rgba(29, 29, 31, ${0.2 * (1 - distance / 100)})`;
                            ctx.lineWidth = 1;
                            ctx.beginPath();
                            ctx.moveTo(particles[a].x, particles[a].y);
                            ctx.lineTo(particles[b].x, particles[b].y);
                            ctx.stroke();
                        }
                    }
                }
            }

            canvas.addEventListener('mousemove', function(event) {
                const rect = canvas.getBoundingClientRect();
                mouse.x = event.clientX - rect.left;
                mouse.y = event.clientY - rect.top;
            });

            canvas.addEventListener('mouseleave', function() {
                mouse.x = null;
                mouse.y = null;
            });

            window.addEventListener('resize', function() {
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                init();
            });

            init();
            animate();
        })();
    </script>

<?php include 'includes/footer.php'; ?>
