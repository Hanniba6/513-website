<?php
/**
 * Checkout Page
 * Student Name: hanniba
 * Student ID: hanniba
 *
 * This page handles the checkout process and saves orders to the database
 */

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['customer_logged_in']) || !$_SESSION['customer_logged_in']) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Load products from JSON file
$productsFile = __DIR__ . '/data/products.json';
$allProducts = [];

if (file_exists($productsFile)) {
    $productsData = file_get_contents($productsFile);
    $allProducts = json_decode($productsData, true);
    if ($allProducts === null) {
        $allProducts = [];
    }
}

// Create a lookup array for products
$productLookup = [];
foreach ($allProducts as $product) {
    $productLookup[$product['id']] = $product;
}

// Calculate cart items and total
$cartItems = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $productId => $quantity) {
    if (isset($productLookup[$productId])) {
        $product = $productLookup[$productId];
        $price = floatval($product['price']);
        $discount = intval($product['discount_percent'] ?? 0);
        $finalPrice = $price * (1 - $discount / 100);
        $itemTotal = $finalPrice * $quantity;

        $cartItems[] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'price' => $finalPrice,
            'quantity' => $quantity,
            'total' => $itemTotal
        ];

        $subtotal += $itemTotal;
    }
}

$tax = $subtotal * 0.1; // 10% tax
$total = $subtotal + $tax;

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Validate shipping address
    $shippingAddress = [
        'full_name' => trim($_POST['shipping_name'] ?? ''),
        'address_line1' => trim($_POST['address_line1'] ?? ''),
        'address_line2' => trim($_POST['address_line2'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'state' => trim($_POST['state'] ?? ''),
        'postal_code' => trim($_POST['postal_code'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
        'phone' => trim($_POST['shipping_phone'] ?? '')
    ];

    // Validate required fields
    if (empty($shippingAddress['full_name']) || empty($shippingAddress['address_line1']) ||
        empty($shippingAddress['city']) || empty($shippingAddress['postal_code']) ||
        empty($shippingAddress['phone'])) {
        $error = "Please fill in all required shipping address fields.";
    } else {
        try {
            // Connect to primary database
            $conn = getDBConnection();

            // Prepare order data
            $customerId = $_SESSION['customer_id'];
            $customerName = $_SESSION['customer_name'];
            $customerEmail = $_SESSION['customer_email'];
            $customerPhone = $_SESSION['customer_phone'] ?? '';
            $orderStatus = 'pending';
            $itemsJson = json_encode($cartItems);
            $shippingAddressJson = json_encode($shippingAddress);

            // Insert order into database with shipping address
            $stmt = $conn->prepare("INSERT INTO orders (customer_id, customer_name, customer_email, customer_phone, total_amount, order_status, items, shipping_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssdsss", $customerId, $customerName, $customerEmail, $customerPhone, $total, $orderStatus, $itemsJson, $shippingAddressJson);

            if ($stmt->execute()) {
                $orderId = $conn->insert_id;

                // Clear cart
                $_SESSION['cart'] = [];

                // Store order ID in session
                $_SESSION['last_order_id'] = $orderId;

                // Redirect to thank you page
                header('Location: thankyou.php');
                exit;
            } else {
                $error = "Failed to place order. Please try again.";
            }

            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $error = "Error placing order: " . $e->getMessage();
        }
    }
}

// Set variables for header
$pageTitle = 'Checkout';
$additionalStyles = '<style>
        .container {
            max-width: 1232px;
            margin: 0 auto;
            padding: 80px 20px;
            background-color: #fbfbfd;
        }

        .page-header {
            margin-bottom: 56px;
        }

        .page-header h1 {
            font-size: 48px;
            font-weight: 700;
            color: #1d1d1f;
            margin: 0 0 16px 0;
            letter-spacing: -1.5px;
        }

        .breadcrumb {
            display: flex;
            gap: 8px;
            font-size: 15px;
            color: #86868b;
            align-items: center;
        }

        .breadcrumb a {
            color: #0071e3;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: #0077ed;
        }

        .checkout-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        @media (max-width: 968px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        .checkout-section {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px;
            border: 1px solid #d2d2d7;
            margin-bottom: 32px;
        }

        .checkout-section h2 {
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 32px 0;
            color: #1d1d1f;
            letter-spacing: -0.5px;
        }

        .customer-info {
            padding: 24px;
            background-color: #f5f5f7;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #d2d2d7;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #86868b;
            font-size: 15px;
        }

        .info-value {
            color: #1d1d1f;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: #1d1d1f;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: #ff3b30;
            margin-left: 4px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            font-size: 15px;
            background: #f5f5f7;
            transition: all 0.3s;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0071e3;
            background: #ffffff;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #d2d2d7;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-details h4 {
            margin: 0 0 8px 0;
            color: #1d1d1f;
            font-size: 17px;
            font-weight: 600;
        }

        .item-details p {
            margin: 0;
            color: #86868b;
            font-size: 15px;
        }

        .item-price {
            font-weight: 700;
            color: #1d1d1f;
            font-size: 17px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            font-size: 17px;
            color: #1d1d1f;
        }

        .summary-row:not(:last-child) {
            border-bottom: 1px solid #d2d2d7;
        }

        .summary-row.total {
            font-size: 24px;
            font-weight: 700;
            padding-top: 24px;
            margin-top: 8px;
        }

        .btn-place-order {
            width: 100%;
            padding: 16px;
            background-color: #0071e3;
            color: #ffffff;
            border: none;
            border-radius: 980px;
            font-weight: 600;
            font-size: 17px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 24px;
        }

        .btn-place-order:hover {
            background-color: #0077ed;
            transform: scale(1.02);
        }

        .btn-back {
            width: 100%;
            padding: 14px;
            background-color: #f5f5f7;
            color: #1d1d1f;
            border: none;
            border-radius: 980px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 12px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-back:hover {
            background-color: #e8e8ed;
        }

        .error-message {
            background-color: #ffe6e6;
            color: #c9302c;
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            border-left: 4px solid #ff3b30;
            font-size: 15px;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 40px;
            }

            .checkout-section {
                padding: 24px;
            }
        }
    </style>';

// Include header
include 'includes/header.php';
?>
    <div class="container">
        <div class="page-header">
            <h1>Checkout</h1>
            <div class="breadcrumb">
                <a href="products.php">Home</a>
                <span>/</span>
                <a href="cart.php">Cart</a>
                <span>/</span>
                <span>Checkout</span>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="checkout-container">
                <div>
                    <div class="checkout-section">
                        <h2>Customer Information</h2>
                        <div class="customer-info">
                            <div class="info-row">
                                <span class="info-label">Name:</span>
                                <span class="info-value"><?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?php echo htmlspecialchars($_SESSION['customer_email']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Phone:</span>
                                <span class="info-value"><?php echo htmlspecialchars($_SESSION['customer_phone'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h2>Shipping Address</h2>
                        <div class="form-group">
                            <label>Full Name <span class="required">*</span></label>
                            <input type="text" name="shipping_name" required value="<?php echo htmlspecialchars($_POST['shipping_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Address Line 1 <span class="required">*</span></label>
                            <input type="text" name="address_line1" required placeholder="Street address, P.O. box" value="<?php echo htmlspecialchars($_POST['address_line1'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Address Line 2</label>
                            <input type="text" name="address_line2" placeholder="Apartment, suite, unit, building, floor, etc." value="<?php echo htmlspecialchars($_POST['address_line2'] ?? ''); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>City <span class="required">*</span></label>
                                <input type="text" name="city" required value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label>State / Province</label>
                                <input type="text" name="state" value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Postal Code <span class="required">*</span></label>
                                <input type="text" name="postal_code" required value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" value="<?php echo htmlspecialchars($_POST['country'] ?? 'United States'); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Phone Number <span class="required">*</span></label>
                            <input type="tel" name="shipping_phone" required value="<?php echo htmlspecialchars($_POST['shipping_phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h2>Order Items</h2>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Quantity: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="item-price">
                                    $<?php echo number_format($item['total'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2>Order Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span>$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <button type="submit" name="place_order" class="btn-place-order">Place Order</button>
                    <a href="cart.php" class="btn-back">Back to Cart</a>
                </div>
            </div>
        </form>
    </div>

<?php include 'includes/footer.php'; ?>
