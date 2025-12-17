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

        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        @media (max-width: 968px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
        }

        .cart-items {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px;
            border: 1px solid #d2d2d7;
        }

        .cart-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #d2d2d7;
        }

        .cart-items-header h2 {
            font-size: 28px;
            font-weight: 600;
            color: #1d1d1f;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 24px;
            padding: 32px 0;
            border-bottom: 1px solid #d2d2d7;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            background-color: #f5f5f7;
            border: 1px solid #d2d2d7;
        }

        .item-details h3 {
            font-size: 21px;
            font-weight: 600;
            margin: 0 0 12px 0;
            color: #1d1d1f;
            letter-spacing: -0.3px;
        }

        .item-price {
            font-size: 17px;
            color: #1d1d1f;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .original-price {
            text-decoration: line-through;
            color: #86868b;
            margin-right: 8px;
            font-weight: 400;
        }

        .discount-badge {
            background: #ff3b30;
            color: #ffffff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
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
            gap: 12px;
            margin: 12px 0;
        }

        .quantity-control label {
            font-size: 15px;
            color: #86868b;
            font-weight: 500;
        }

        .quantity-control input {
            width: 70px;
            padding: 8px 12px;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            text-align: center;
            font-size: 15px;
            background: #f5f5f7;
            transition: all 0.3s;
        }

        .quantity-control input:focus {
            outline: none;
            border-color: #0071e3;
            background: #ffffff;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 980px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-update {
            background-color: #0071e3;
            color: #ffffff;
        }

        .btn-update:hover {
            background-color: #0077ed;
            transform: scale(1.05);
        }

        .btn-remove {
            background-color: #ff3b30;
            color: #ffffff;
        }

        .btn-remove:hover {
            background-color: #ff2d20;
            transform: scale(1.05);
        }

        .item-total {
            font-size: 24px;
            font-weight: 600;
            color: #1d1d1f;
        }

        .cart-summary {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px;
            border: 1px solid #d2d2d7;
            height: fit-content;
            position: sticky;
            top: 80px;
        }

        .cart-summary h2 {
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 32px 0;
            color: #1d1d1f;
            letter-spacing: -0.5px;
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

        .btn-checkout {
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-checkout:hover {
            background-color: #0077ed;
            transform: scale(1.02);
        }

        .btn-continue {
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

        .btn-continue:hover {
            background-color: #e8e8ed;
        }

        .empty-cart {
            text-align: center;
            padding: 80px 40px;
        }

        .empty-cart-icon {
            font-size: 72px;
            margin-bottom: 24px;
        }

        .empty-cart h2 {
            font-size: 32px;
            color: #1d1d1f;
            margin-bottom: 16px;
            font-weight: 700;
            letter-spacing: -0.8px;
        }

        .empty-cart p {
            color: #86868b;
            margin-bottom: 32px;
            font-size: 17px;
        }

        .btn-clear {
            padding: 10px 24px;
            background-color: #ff3b30;
            color: #ffffff;
            border: none;
            border-radius: 980px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-clear:hover {
            background-color: #ff2d20;
            transform: scale(1.05);
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
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#86868b" stroke-width="1.5">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <h2>Your cart is empty</h2>
                    <p>Add some products to get started!</p>
                    <a href="products.php" class="btn-checkout">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <div class="cart-items-header">
                        <h2>Cart Items (<?php echo count($cartItems); ?>)</h2>
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
                                        <span class="discount-badge">-<?php echo $item['discount']; ?>%</span>
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
