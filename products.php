<?php
/**
 * Products Display Page
 * Student Name: hanniba
 * Student ID: hanniba
 *
 * This page displays graphics cards from products.json in a responsive grid layout
 */

session_start();
require_once 'config.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get user email from URL parameter
$current_user_email = isset($_GET['user_email']) ? htmlspecialchars($_GET['user_email']) : '';
$show_reg_success = false;
$show_login_success = false;

// Function to set customer session from database
function setCustomerSession($email) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, name, email, phone FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
            $_SESSION['customer_logged_in'] = true;
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            $_SESSION['customer_email'] = $customer['email'];
            $_SESSION['customer_phone'] = $customer['phone'] ?? '';
            return true;
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        error_log("Error setting customer session: " . $e->getMessage());
    }
    return false;
}

// Check for registration success
if (isset($_GET['reg_status']) && $_GET['reg_status'] == 'success' && $current_user_email) {
    $show_reg_success = true;
    if (setCustomerSession($current_user_email)) {
        // Session set successfully
    } else {
        // Fallback if customer not found in database
        $_SESSION['customer_logged_in'] = true;
        $_SESSION['customer_email'] = $current_user_email;
        $_SESSION['customer_name'] = $current_user_email;
        $_SESSION['customer_id'] = 0; // Temporary ID
    }
}

// Check for login success
if (isset($_GET['login_status']) && $_GET['login_status'] == 'success' && $current_user_email) {
    $show_login_success = true;
    if (setCustomerSession($current_user_email)) {
        // Session set successfully
    } else {
        // Fallback if customer not found in database
        $_SESSION['customer_logged_in'] = true;
        $_SESSION['customer_email'] = $current_user_email;
        $_SESSION['customer_name'] = $current_user_email;
        $_SESSION['customer_id'] = 0; // Temporary ID
    }
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id > 0 && $quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }

        header('Location: products.php?added=' . $product_id);
        exit;
    }
}

// Load products from JSON file
$productsFile = __DIR__ . '/data/products.json';
$products = [];

if (file_exists($productsFile)) {
    $productsData = file_get_contents($productsFile);
    $products = json_decode($productsData, true);
    if ($products === null) {
        $products = [];
    }
}

// Group products by category
$productsByCategory = [];
foreach ($products as $product) {
    $category = $product['category'] ?? 'Uncategorized';
    if (!isset($productsByCategory[$category])) {
        $productsByCategory[$category] = [];
    }
    $productsByCategory[$category][] = $product;
}

// Calculate cart count
$cartCount = array_sum($_SESSION['cart']);

// Set variables for header
$pageTitle = 'Graphics Cards';
$additionalStyles = '<style>
        /* Success Notification */
        .success-notification {
            position: fixed;
            top: 80px;
            right: 20px;
            background: #34c759;
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(52, 199, 89, 0.4);
            z-index: 1000;
            animation: slideIn 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Hero Section - White to Gray Gradient */
        .hero-section {
            position: relative;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f5f7 0%, #d2d2d7 100%);
            overflow: hidden;
        }

        /* Particle Canvas */
        #particles-canvas {
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
            text-align: center;
            color: #1d1d1f;
            max-width: 800px;
            padding: 0 20px;
        }

        .hero-title {
            font-size: 64px;
            font-weight: 700;
            margin: 0 0 20px 0;
            color: #1d1d1f;
            letter-spacing: -2px;
            line-height: 1.1;
        }

        .hero-description {
            font-size: 24px;
            line-height: 1.5;
            margin-bottom: 40px;
            color: #424245;
            font-weight: 400;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: #1d1d1f;
            color: #ffffff;
            padding: 14px 32px;
            border-radius: 980px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-hero-primary:hover {
            background: #000000;
            transform: scale(1.05);
        }

        .btn-hero-outline {
            background-color: transparent;
            color: #1d1d1f;
            padding: 14px 32px;
            border: 2px solid #1d1d1f;
            border-radius: 980px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .btn-hero-outline:hover {
            background-color: #1d1d1f;
            color: #ffffff;
            transform: scale(1.05);
        }

        /* Filter Section */
        .filter-section {
            background: #ffffff;
            border-bottom: 1px solid #d2d2d7;
            position: sticky;
            top: 60px;
            z-index: 40;
        }

        .filter-container {
            max-width: 1232px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #1d1d1f;
        }

        .filter-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 20px;
            background: #f5f5f7;
            color: #1d1d1f;
            border: 1px solid #d2d2d7;
            border-radius: 980px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            background: #e8e8ed;
        }

        .filter-btn.active {
            background: #0071e3;
            color: #ffffff;
            border-color: #0071e3;
        }

        /* Main Content */
        .main-content {
            background-color: #fbfbfd;
            padding: 80px 0;
            min-height: 400px;
            position: relative;
        }

        /* Particle Container for Main Content */
        #main-particles-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .content-wrapper {
            max-width: 1232px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .category-section {
            margin-bottom: 80px;
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 48px;
        }

        .category-title {
            font-size: 40px;
            font-weight: 700;
            color: #1d1d1f;
            letter-spacing: -1px;
        }

        .category-count {
            font-size: 16px;
            color: #86868b;
            font-weight: 500;
        }

        /* Product Cards */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 32px;
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }
        }

        .product-card {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #d2d2d7;
        }

        .product-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        .product-card.hidden {
            display: none;
        }

        .product-image-wrapper {
            width: 100%;
            height: 320px;
            background: #f5f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 24px;
        }

        .product-name {
            font-size: 21px;
            font-weight: 600;
            color: #1d1d1f;
            margin: 0 0 12px 0;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        .product-description {
            color: #86868b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .product-price-section {
            margin: 16px 0;
        }

        .product-price {
            font-size: 24px;
            font-weight: 600;
            color: #1d1d1f;
            display: inline-block;
        }

        .original-price {
            text-decoration: line-through;
            color: #86868b;
            font-size: 17px;
            margin-right: 12px;
            font-weight: 400;
        }

        .discount-badge {
            background: #ff3b30;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 12px;
            background: #0071e3;
            color: #ffffff;
            border: none;
            border-radius: 980px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 16px;
        }

        .add-to-cart-btn:hover {
            background: #0077ed;
            transform: scale(1.02);
        }

        .admin-links {
            text-align: center;
            margin-top: 60px;
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .admin-link {
            display: inline-block;
            padding: 12px 28px;
            background: #f5f5f7;
            color: #1d1d1f;
            text-decoration: none;
            border-radius: 980px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .admin-link:hover {
            background: #e8e8ed;
            transform: scale(1.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 40px;
            }

            .hero-description {
                font-size: 18px;
            }

            .category-title {
                font-size: 32px;
            }

            .hero-buttons {
                flex-direction: column;
                width: 100%;
            }

            .btn-hero-primary, .btn-hero-outline {
                width: 100%;
            }

            .filter-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .category-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>';

// Include header
include 'includes/header.php';
?>

    <!-- Notifications -->
    <?php if ($show_reg_success): ?>
        <div class="success-notification" id="regSuccessNotification">
            Registration successful! Welcome, <?php echo htmlspecialchars($current_user_email); ?>
        </div>
        <script>
            setTimeout(function() {
                var notification = document.getElementById('regSuccessNotification');
                if (notification) {
                    notification.style.opacity = '0';
                    setTimeout(function() { notification.remove(); }, 300);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <?php if ($show_login_success): ?>
        <div class="success-notification" id="loginSuccessNotification">
            Login successful! Welcome back, <?php echo htmlspecialchars($current_user_email); ?>
        </div>
        <script>
            setTimeout(function() {
                var notification = document.getElementById('loginSuccessNotification');
                if (notification) {
                    notification.style.opacity = '0';
                    setTimeout(function() { notification.remove(); }, 300);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['added'])): ?>
        <div class="success-notification" id="successNotification">
            Product added to cart successfully!
        </div>
        <script>
            setTimeout(function() {
                var notification = document.getElementById('successNotification');
                if (notification) {
                    notification.style.opacity = '0';
                    setTimeout(function() { notification.remove(); }, 300);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
        <div class="success-notification" id="loginNotification">
            Login successful! Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['customer_name'])[0]); ?>!
        </div>
        <script>
            setTimeout(function() {
                var notification = document.getElementById('loginNotification');
                if (notification) {
                    notification.style.opacity = '0';
                    setTimeout(function() { notification.remove(); }, 300);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <canvas id="particles-canvas"></canvas>
        <div class="hero-content">
            <h1 class="hero-title">Hanniba Store</h1>
            <p class="hero-description">Premium graphics cards for gaming and professional work. Experience next-level performance.</p>
            <div class="hero-buttons">
                <a href="#products" class="btn-hero-primary">Explore Products</a>
                <a href="admin/login.php" class="btn-hero-outline">Admin Panel</a>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="filter-container">
            <span class="filter-label">Filter by:</span>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All Products</button>
                <?php foreach ($productsByCategory as $category => $categoryProducts): ?>
                    <button class="filter-btn" data-filter="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category))); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <main class="main-content" id="products">
        <canvas id="main-particles-canvas"></canvas>
        <div class="content-wrapper">

            <?php if (empty($productsByCategory)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <p style="color: #86868b; font-size: 1.2em;">No products available at this time.</p>
                </div>
            <?php else: ?>
                <?php foreach ($productsByCategory as $category => $categoryProducts): ?>
                    <div class="category-section" data-category="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category))); ?>">
                        <div class="category-header">
                            <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
                            <span class="category-count"><?php echo count($categoryProducts); ?> products</span>
                        </div>
                        <div class="products-grid">
                            <?php foreach ($categoryProducts as $product): ?>
                                <div class="product-card" data-category="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category))); ?>">
                                    <?php if (!empty($product['image_url'])): ?>
                                    <div class="product-image-wrapper">
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                                             alt="<?php echo htmlspecialchars($product['name'] ?? ''); ?>"
                                             class="product-image"
                                             onerror="this.style.display='none'">
                                    </div>
                                    <?php endif; ?>

                                    <div class="product-info">
                                        <h3 class="product-name"><?php echo htmlspecialchars($product['name'] ?? 'Unnamed Product'); ?></h3>

                                        <p class="product-description">
                                            <?php
                                            $description = $product['description'] ?? 'High-performance graphics card for gaming and professional work.';
                                            $description = htmlspecialchars($description);
                                            if (strlen($description) > 120) {
                                                $description = substr($description, 0, 120) . '...';
                                            }
                                            echo $description;
                                            ?>
                                        </p>

                                        <div class="product-price-section">
                                            <?php
                                            $price = floatval($product['price'] ?? 0);
                                            $discount = intval($product['discount_percent'] ?? 0);
                                            $finalPrice = $price * (1 - $discount / 100);

                                            if ($discount > 0) {
                                                echo '<span class="original-price">$' . number_format($price, 2) . '</span>';
                                            }
                                            echo '<span class="product-price">$' . number_format($finalPrice, 2) . '</span>';
                                            if ($discount > 0) {
                                                echo '<span class="discount-badge">-' . $discount . '%</span>';
                                            }
                                            ?>
                                        </div>

                                        <form method="POST">
                                            <input type="hidden" name="action" value="add_to_cart">
                                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="admin-links">
                <a href="admin/login.php" class="admin-link">Admin Panel</a>
                <a href="list_customers.php" class="admin-link">Customer List</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Particle Effect for Hero Section
        (function() {
            const canvas = document.getElementById('particles-canvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            const particles = [];
            const particleCount = 50;
            const mouse = { x: null, y: null, radius: 150 };

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 3 + 1;
                    this.speedX = Math.random() * 1 - 0.5;
                    this.speedY = Math.random() * 1 - 0.5;
                }

                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
                    if (this.y > canvas.height || this.y < 0) this.speedY *= -1;

                    // Mouse interaction
                    if (mouse.x != null && mouse.y != null) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < mouse.radius) {
                            const force = (mouse.radius - distance) / mouse.radius;
                            const dirX = dx / distance;
                            const dirY = dy / distance;
                            this.x -= dirX * force * 2;
                            this.y -= dirY * force * 2;
                        }
                    }
                }

                draw() {
                    ctx.fillStyle = 'rgba(29, 29, 31, 0.5)';
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

        // Enhanced Particle Effect for Main Content (Around Products)
        (function() {
            const canvas = document.getElementById('main-particles-canvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const mainContent = document.querySelector('.main-content');

            function resizeCanvas() {
                canvas.width = mainContent.offsetWidth;
                canvas.height = mainContent.offsetHeight;
            }
            resizeCanvas();

            const particles = [];
            const particleCount = 120; // Increased particle count for better visibility
            const mouse = { x: null, y: null, radius: 180 };

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 3.5 + 1.5; // Larger particles
                    this.speedX = Math.random() * 0.8 - 0.4;
                    this.speedY = Math.random() * 0.8 - 0.4;
                    this.opacity = Math.random() * 0.5 + 0.3; // Variable opacity
                }

                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
                    if (this.y > canvas.height || this.y < 0) this.speedY *= -1;

                    // Enhanced mouse interaction
                    if (mouse.x != null && mouse.y != null) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < mouse.radius) {
                            const force = (mouse.radius - distance) / mouse.radius;
                            const dirX = dx / distance;
                            const dirY = dy / distance;
                            this.x -= dirX * force * 3; // Stronger interaction
                            this.y -= dirY * force * 3;
                        }
                    }
                }

                draw() {
                    // More visible particles with gradient effect
                    const gradient = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, this.size);
                    gradient.addColorStop(0, `rgba(0, 113, 227, ${this.opacity})`); // Apple blue
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

            // Connect nearby particles with lines
            function connect() {
                for (let a = 0; a < particles.length; a++) {
                    for (let b = a + 1; b < particles.length; b++) {
                        const dx = particles[a].x - particles[b].x;
                        const dy = particles[a].y - particles[b].y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < 120) {
                            ctx.strokeStyle = `rgba(0, 113, 227, ${0.15 * (1 - distance / 120)})`;
                            ctx.lineWidth = 1;
                            ctx.beginPath();
                            ctx.moveTo(particles[a].x, particles[a].y);
                            ctx.lineTo(particles[b].x, particles[b].y);
                            ctx.stroke();
                        }
                    }
                }
            }

            // Track mouse movement relative to main content
            mainContent.addEventListener('mousemove', function(event) {
                const rect = canvas.getBoundingClientRect();
                mouse.x = event.clientX - rect.left;
                mouse.y = event.clientY - rect.top;
            });

            mainContent.addEventListener('mouseleave', function() {
                mouse.x = null;
                mouse.y = null;
            });

            window.addEventListener('resize', function() {
                resizeCanvas();
                init();
            });

            init();
            animate();
        })();

        // Filter functionality
        (function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const categorySections = document.querySelectorAll('.category-section');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');

                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    if (filter === 'all') {
                        categorySections.forEach(section => {
                            section.style.display = 'block';
                        });
                    } else {
                        categorySections.forEach(section => {
                            if (section.getAttribute('data-category') === filter) {
                                section.style.display = 'block';
                            } else {
                                section.style.display = 'none';
                            }
                        });
                    }

                    // Smooth scroll to products section
                    document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
                });
            });
        })();
    </script>
