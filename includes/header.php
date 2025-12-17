<?php
/**
 * Global Header Component
 * This header is used across all pages with consistent navigation
 */

// Calculate cart count if cart exists
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Hanniba Store' : 'Hanniba Store - Premium Graphics Cards'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Inter', 'Segoe UI', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        /* Apple-style Global Styles */
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
            background-color: #fbfbfd;
            color: #1d1d1f;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        /* Sticky Header */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background-color: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .header-container {
            max-width: 1232px;
            margin: 0 auto;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Brand Logo */
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .brand-logo:hover {
            opacity: 0.7;
        }

        .brand-logo-text {
            font-size: 21px;
            font-weight: 600;
            color: #1d1d1f;
            letter-spacing: -0.5px;
        }

        /* Navigation */
        .main-nav {
            display: flex;
            gap: 32px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .main-nav a {
            color: #1d1d1f;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.2px;
            transition: color 0.3s;
            position: relative;
        }

        .main-nav a:hover {
            color: #2997ff;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .cart-link {
            position: relative;
            color: #1d1d1f;
            text-decoration: none;
            font-size: 20px;
            transition: all 0.3s;
            padding: 6px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-link:hover {
            background-color: rgba(0, 0, 0, 0.04);
            transform: scale(1.05);
        }

        .cart-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background-color: #ff3b30;
            color: white;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            padding: 0 5px;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }

        .user-section {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .user-name {
            color: #1d1d1f;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-auth {
            padding: 8px 18px;
            background-color: #0071e3;
            color: white;
            border: none;
            border-radius: 980px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-auth:hover {
            background-color: #0077ed;
            transform: scale(1.05);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-nav {
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 12px 15px;
            }

            .main-nav {
                gap: 12px;
            }

            .main-nav a {
                font-size: 12px;
            }

            .brand-logo-text {
                font-size: 18px;
            }

            .header-actions {
                gap: 12px;
            }
        }
    </style>
    <?php if (isset($additionalStyles)) echo $additionalStyles; ?>
</head>
<body>
    <!-- Main Header -->
    <header class="main-header">
        <div class="header-container">
            <!-- Brand Logo with SVG -->
            <a href="<?php echo isset($cssPath) ? $cssPath : './'; ?>products.php" class="brand-logo">
                <svg viewBox="0 0 48 48" class="w-8 h-8 text-gray-900" fill="currentColor" aria-label="Brand Logo">
                    <rect x="8" y="4" width="8" height="40" rx="2"/>
                    <rect x="20" y="10" width="8" height="28" rx="2"/>
                    <rect x="32" y="4" width="8" height="40" rx="2"/>
                </svg>
                <span class="brand-logo-text">Hanniba Store</span>
            </a>

            <!-- Navigation -->
            <nav>
                <ul class="main-nav">
                    <li><a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>products.php">Home</a></li>
                    <li><a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>about.php">About</a></li>
                    <li><a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>order_history.php">Orders</a></li>
                    <li><a href="http://182.61.1.142/forum/">Forum</a></li>
                    <li><a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>feedback.php">Feedback</a></li>
                    <li><a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>recruitment.php">Careers</a></li>
                    <li><a href="http://182.61.1.142/254-2/">List</a></li>
                </ul>
            </nav>

            <!-- Header Actions -->
            <div class="header-actions">
                <a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>cart.php" class="cart-link" title="Shopping Cart">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>

                <div class="user-section">
                    <?php if (isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in']): ?>
                        <span class="user-name">Hi, <?php echo htmlspecialchars(explode(' ', $_SESSION['customer_name'])[0]); ?></span>
                        <a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>logout.php" class="btn-auth">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo isset($cssPath) ? $cssPath : ''; ?>login.php" class="btn-auth">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
