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

        .order-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .order-id {
            font-size: 20px;
            font-weight: 700;
            color: #333333;
        }

        .order-date {
            color: #666666;
            font-size: 14px;
        }

        .order-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cfe2ff;
            color: #084298;
        }

        .status-shipped {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-delivered {
            background-color: #d1e7dd;
            color: #0a3622;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #842029;
        }

        .order-items {
            margin: 20px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 600;
            color: #333333;
        }

        .item-qty {
            color: #666666;
            font-size: 14px;
        }

        .item-price {
            font-weight: 700;
            color: #007bff;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            font-size: 20px;
            font-weight: 700;
            color: #333333;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: #ffffff;
            border-radius: 8px;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            font-size: 28px;
            color: #333333;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666666;
            margin-bottom: 30px;
        }

        .btn-shop {
            padding: 14px 32px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-shop:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
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
                <div class="empty-icon">📦</div>
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

                    <div class="order-total">
                        <span>Total Amount:</span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php include 'includes/footer.php'; ?>
