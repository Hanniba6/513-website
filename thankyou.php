<?php
/**
 * Thank You Page
 * Student Name: hanniba
 * Student ID: hanniba
 *
 * This page displays order confirmation after successful checkout
 */

session_start();
require_once 'config.php';

// Check if there's a last order ID
if (!isset($_SESSION['last_order_id'])) {
    header('Location: products.php');
    exit;
}

$orderId = $_SESSION['last_order_id'];
$orderDetails = null;

try {
    // Connect to primary database
    $conn = getDBConnection();

    // Fetch order details
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $orderDetails = $result->fetch_assoc();
        $orderDetails['items'] = json_decode($orderDetails['items'], true);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $error = "Error fetching order details: " . $e->getMessage();
}

// Clear the last order ID from session
unset($_SESSION['last_order_id']);

// Set variables for header
$pageTitle = 'Order Confirmation';
$additionalStyles = '<style>
        body {
            background-color: #f8f8f8;
            padding-top: 80px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .page-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            max-width: 700px;
            margin: 40px 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 40px;
            text-align: center;
            color: #ffffff;
        }

        .success-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .header p {
            font-size: 16px;
            margin: 0;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .order-info {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
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
            font-weight: 500;
        }

        .order-items {
            margin: 30px 0;
        }

        .order-items h3 {
            font-size: 20px;
            font-weight: 700;
            color: #333333;
            margin: 0 0 20px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-details p {
            margin: 0;
            color: #333333;
        }

        .item-details .item-qty {
            color: #666666;
            font-size: 14px;
            margin-top: 5px;
        }

        .item-price {
            font-weight: 700;
            color: #007bff;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-size: 20px;
            font-weight: 700;
            color: #333333;
            border-top: 2px solid #333333;
            margin-top: 20px;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background-color: #007bff;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #007bff;
            border: 2px solid #007bff;
        }

        .btn-secondary:hover {
            background-color: #007bff;
            color: #ffffff;
        }
    </style>';

// Include header
include 'includes/header.php';
?>

    <div class="page-wrapper">
        <div class="container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your purchase</p>
        </div>

        <div class="content">
            <?php if ($orderDetails): ?>
                <div class="order-info">
                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#<?php echo htmlspecialchars($orderDetails['id']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value"><?php echo date('F j, Y, g:i a', strtotime($orderDetails['order_date'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Customer Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($orderDetails['customer_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($orderDetails['customer_email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value" style="color: #28a745; text-transform: capitalize;"><?php echo htmlspecialchars($orderDetails['order_status']); ?></span>
                    </div>
                </div>

                <div class="order-items">
                    <h3>Order Items</h3>
                    <?php foreach ($orderDetails['items'] as $item): ?>
                        <div class="order-item">
                            <div class="item-details">
                                <p><strong><?php echo htmlspecialchars($item['name']); ?></strong></p>
                                <p class="item-qty">Quantity: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="item-price">
                                $<?php echo number_format($item['total'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="total-row">
                        <span>Total Amount:</span>
                        <span>$<?php echo number_format($orderDetails['total_amount'], 2); ?></span>
                    </div>
                </div>

                <p style="color: #666666; text-align: center; margin: 30px 0;">
                    A confirmation email has been sent to <strong><?php echo htmlspecialchars($orderDetails['customer_email']); ?></strong>
                </p>

                <div class="actions">
                    <a href="order_history.php" class="btn btn-secondary">View Order History</a>
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666666;">Order details not found.</p>
                <div class="actions">
                    <a href="products.php" class="btn btn-primary">Back to Products</a>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
