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

        // Insert order into database
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, customer_name, customer_email, customer_phone, total_amount, order_status, items) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdss", $customerId, $customerName, $customerEmail, $customerPhone, $total, $orderStatus, $itemsJson);

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

// Set variables for header
$pageTitle = 'Checkout';
$additionalStyles = '<style>
        body {
            background-color: #f8f8f8;
            padding-top: 80px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #333333;
            margin: 0 0 10px 0;
        }

        .breadcrumb {
            display: flex;
            gap: 10px;
            font-size: 14px;
            color: #666666;
        }

        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }

        .checkout-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        @media (max-width: 968px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        .checkout-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .checkout-section h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 20px 0;
            color: #333333;
        }

        .customer-info {
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666666;
        }

        .info-value {
            color: #333333;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-details h4 {
            margin: 0 0 5px 0;
            color: #333333;
            font-size: 16px;
        }

        .item-details p {
            margin: 0;
            color: #666666;
            font-size: 14px;
        }

        .item-price {
            font-weight: 700;
            color: #007bff;
            font-size: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .summary-row.total {
            border-bottom: none;
            font-size: 20px;
            font-weight: 700;
            color: #333333;
            margin-top: 10px;
        }

        .btn-place-order {
            width: 100%;
            padding: 16px;
            background-color: #28a745;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-place-order:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-back {
            width: 100%;
            padding: 12px;
            background-color: #ffffff;
            color: #007bff;
            border: 2px solid #007bff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-back:hover {
            background-color: #007bff;
            color: #ffffff;
        }

        .error-message {
            background-color: #fee;
            color: #c33;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
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

                    <div class="checkout-section" style="margin-top: 30px;">
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
