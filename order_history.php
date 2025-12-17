<?php
/**
 * Order History Page
 * Student Name: hanniba
 * Student ID: hanniba
 *
 * This page displays the customer's order history
 */

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['customer_logged_in']) || !$_SESSION['customer_logged_in']) {
    header('Location: login.php');
    exit;
}

$customerId = $_SESSION['customer_id'];
$orders = [];

try {
    // Connect to primary database
    $conn = getDBConnection();

    // Fetch orders for this customer
    $stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $row['items'] = json_decode($row['items'], true);
        $row['shipping_address'] = isset($row['shipping_address']) ? json_decode($row['shipping_address'], true) : null;
        $orders[] = $row;
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}

// Set variables for header
$pageTitle = 'Order History';
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

        .order-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px;
            border: 1px solid #d2d2d7;
            margin-bottom: 32px;
            transition: all 0.3s;
        }

        .order-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 24px;
            border-bottom: 1px solid #d2d2d7;
            margin-bottom: 24px;
        }

        .order-id {
            font-size: 24px;
            font-weight: 700;
            color: #1d1d1f;
            letter-spacing: -0.5px;
        }

        .order-date {
            color: #86868b;
            font-size: 15px;
            margin-top: 8px;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 980px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-pending {
            background-color: #fff9e6;
            color: #856404;
        }

        .status-processing {
            background-color: #e6f2ff;
            color: #0071e3;
        }

        .status-shipped {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-delivered {
            background-color: #d1f4e0;
            color: #0d5028;
        }

        .status-cancelled {
            background-color: #ffe6e6;
            color: #c9302c;
        }

        .order-items {
            margin: 24px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #f5f5f7;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 600;
            color: #1d1d1f;
            font-size: 17px;
        }

        .item-qty {
            color: #86868b;
            font-size: 15px;
            margin-top: 4px;
        }

        .item-price {
            font-weight: 700;
            color: #1d1d1f;
            font-size: 17px;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            padding-top: 24px;
            border-top: 1px solid #d2d2d7;
            font-size: 24px;
            font-weight: 700;
            color: #1d1d1f;
            margin-top: 24px;
        }

        .shipping-address {
            margin: 24px 0;
            padding: 24px;
            background: #f5f5f7;
            border-radius: 12px;
            border: 1px solid #d2d2d7;
        }

        .shipping-address h3 {
            font-size: 17px;
            font-weight: 600;
            color: #1d1d1f;
            margin: 0 0 16px 0;
        }

        .address-line {
            font-size: 15px;
            color: #1d1d1f;
            line-height: 1.6;
            margin: 4px 0;
        }

        .address-phone {
            font-size: 15px;
            color: #86868b;
            margin-top: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 120px 40px;
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #d2d2d7;
        }

        .empty-icon {
            margin-bottom: 24px;
        }

        .empty-state h2 {
            font-size: 32px;
            color: #1d1d1f;
            margin-bottom: 16px;
            font-weight: 700;
            letter-spacing: -0.8px;
        }

        .empty-state p {
            color: #86868b;
            margin-bottom: 32px;
            font-size: 17px;
        }

        .btn-shop {
            padding: 16px 40px;
            background-color: #0071e3;
            color: #ffffff;
            border: none;
            border-radius: 980px;
            font-weight: 600;
            font-size: 17px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-shop:hover {
            background-color: #0077ed;
            transform: scale(1.05);
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

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .order-card {
                padding: 24px;
            }
        }
    </style>';

// Include header
include 'includes/header.php';
?>
    <div class="container">
        <div class="page-header">
            <h1>Order History</h1>
            <div class="breadcrumb">
                <a href="products.php">Home</a>
                <span>/</span>
                <span>Order History</span>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <svg class="empty-icon" width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#86868b" stroke-width="1.5">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                <h2>No Orders Yet</h2>
                <p>You haven't placed any orders. Start shopping to see your orders here!</p>
                <a href="products.php" class="btn-shop">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo htmlspecialchars($order['id']); ?></div>
                            <div class="order-date">Placed on <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></div>
                        </div>
                        <div class="order-status status-<?php echo htmlspecialchars($order['order_status']); ?>">
                            <?php echo htmlspecialchars($order['order_status']); ?>
                        </div>
                    </div>

                    <div class="order-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <div>
                                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="item-qty">Quantity: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price'], 2); ?></div>
                                </div>
                                <div class="item-price">
                                    $<?php echo number_format($item['total'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (isset($order['shipping_address']) && !empty($order['shipping_address'])): ?>
                        <div class="shipping-address">
                            <h3>Shipping Address</h3>
                            <div class="address-line"><strong><?php echo htmlspecialchars($order['shipping_address']['full_name']); ?></strong></div>
                            <div class="address-line"><?php echo htmlspecialchars($order['shipping_address']['address_line1']); ?></div>
                            <?php if (!empty($order['shipping_address']['address_line2'])): ?>
                                <div class="address-line"><?php echo htmlspecialchars($order['shipping_address']['address_line2']); ?></div>
                            <?php endif; ?>
                            <div class="address-line">
                                <?php echo htmlspecialchars($order['shipping_address']['city']); ?><?php echo !empty($order['shipping_address']['state']) ? ', ' . htmlspecialchars($order['shipping_address']['state']) : ''; ?> <?php echo htmlspecialchars($order['shipping_address']['postal_code']); ?>
                            </div>
                            <?php if (!empty($order['shipping_address']['country'])): ?>
                                <div class="address-line"><?php echo htmlspecialchars($order['shipping_address']['country']); ?></div>
                            <?php endif; ?>
                            <div class="address-phone">Phone: <?php echo htmlspecialchars($order['shipping_address']['phone']); ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="order-total">
                        <span>Total Amount:</span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php include 'includes/footer.php'; ?>
