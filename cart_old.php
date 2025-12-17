<?php
/**
 * Shopping Cart Page
 * Student Name: hanniba
 * Student ID: hanniba
 *
 * This page displays the shopping cart and allows users to manage cart items
 */

session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
                break;

            case 'remove':
                $product_id = intval($_POST['product_id']);
                unset($_SESSION['cart'][$product_id]);
                break;

            case 'clear':
                $_SESSION['cart'] = [];
                break;
        }
        header('Location: cart.php');
        exit;
    }
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
            'id' => $productId,
            'name' => $product['name'],
            'image_url' => $product['image_url'] ?? '',
            'price' => $price,
            'discount' => $discount,
            'final_price' => $finalPrice,
            'quantity' => $quantity,
            'total' => $itemTotal
        ];

        $subtotal += $itemTotal;
    }
}

$tax = $subtotal * 0.1; // 10% tax
$total = $subtotal + $tax;

// Set variables for header
$pageTitle = 'Shopping Cart';
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

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        @media (max-width: 968px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
        }

        .cart-items {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #f0f0f0;
        }

        .item-details h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #333333;
        }

        .item-price {
            font-size: 16px;
            color: #007bff;
            font-weight: 600;
        }

        .original-price {
            text-decoration: line-through;
            color: #999999;
            margin-right: 10px;
        }

        .item-controls {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .quantity-control input {
            width: 60px;
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            text-align: center;
            font-size: 14px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-update {
            background-color: #007bff;
            color: #ffffff;
        }

        .btn-update:hover {
            background-color: #0056b3;
        }

        .btn-remove {
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-remove:hover {
            background-color: #c82333;
        }

        .item-total {
            font-size: 20px;
            font-weight: 700;
            color: #333333;
        }

        .cart-summary {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .cart-summary h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 20px 0;
            color: #333333;
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

        .btn-checkout {
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

        .btn-checkout:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-continue {
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

        .btn-continue:hover {
            background-color: #007bff;
            color: #ffffff;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-cart h2 {
            font-size: 28px;
            color: #333333;
            margin-bottom: 10px;
        }

        .empty-cart p {
            color: #666666;
            margin-bottom: 30px;
        }

        .btn-clear {
            padding: 10px 20px;
            background-color: #dc3545;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-clear:hover {
            background-color: #c82333;
        }
    </style>';

// Include header
include 'includes/header.php';
?>
    <div class="container">
        <div class="page-header">
            <h1>Shopping Cart</h1>
            <div class="breadcrumb">
                <a href="products.php">Home</a>
                <span>/</span>
                <span>Cart</span>
            </div>
        </div>

        <?php if (empty($cartItems)): ?>
            <div class="cart-items">
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h2>Your cart is empty</h2>
                    <p>Add some products to get started!</p>
                    <a href="products.php" class="btn-checkout">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0;">Cart Items (<?php echo count($cartItems); ?>)</h2>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn-clear" onclick="return confirm('Are you sure you want to clear the cart?');">Clear Cart</button>
                        </form>
                    </div>

                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <div>
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="item-image"
                                         onerror="this.style.display='none'">
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="item-price">
                                    <?php if ($item['discount'] > 0): ?>
                                        <span class="original-price">$<?php echo number_format($item['price'], 2); ?></span>
                                    <?php endif; ?>
                                    $<?php echo number_format($item['final_price'], 2); ?>
                                    <?php if ($item['discount'] > 0): ?>
                                        <span style="color: #e74c3c; font-size: 14px; margin-left: 10px;">-<?php echo $item['discount']; ?>% OFF</span>
                                    <?php endif; ?>
                                </div>
                                <form method="POST" class="quantity-control">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <label>Qty:</label>
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99">
                                    <button type="submit" class="btn btn-update">Update</button>
                                </form>
                            </div>
                            <div class="item-controls">
                                <div class="item-total">$<?php echo number_format($item['total'], 2); ?></div>
                                <form method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-remove">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
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
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                    <a href="products.php" class="btn-continue">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php include 'includes/footer.php'; ?>
