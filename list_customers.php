<?php
/**
 * Customer List Page
 * Student Name: hanniba
 * Student ID: hanniba
 *
 * This page displays customers from the FluentCRM database
 */

session_start();
require_once 'config.php';

$customers = [];
$error = '';

try {
    // Connect to FluentCRM database (ghb7zzwh6fy5j3yl)
    $conn = getDBConnection();

    // Fetch customers from wp_fc_subscribers table
    $query = "SELECT id, first_name, last_name, email, phone, status, created_at FROM wp_fc_subscribers ORDER BY created_at DESC";
    $result = $conn->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
    }

    $conn->close();
} catch (Exception $e) {
    $error = "Error fetching customers: " . $e->getMessage();
}

// Set variables for header
$pageTitle = 'Customer List';
$additionalStyles = '<style>
        body {
            background-color: #f8f8f8;
            padding-top: 80px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #333333;
            margin: 0;
        }

        .btn-back {
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .customers-table {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f8f8f8;
        }

        thead th {
            padding: 16px;
            text-align: left;
            font-weight: 700;
            color: #333333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e0e0e0;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f8f8f8;
        }

        tbody td {
            padding: 16px;
            color: #333333;
            font-size: 14px;
        }

        .customer-name {
            font-weight: 600;
            color: #333333;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-subscribed {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-unsubscribed {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .error-message {
            background-color: #f8d7da;
            color: #842029;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #842029;
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
        }

        @media (max-width: 768px) {
            .customers-table {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }
        }
    </style>';

// Include header
include 'includes/header.php';
?>
    <div class="container">
        <div class="page-header">
            <h1>Customer List</h1>
            <a href="products.php" class="btn-back">← Back to Products</a>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (empty($customers)): ?>
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <h2>No Customers Found</h2>
                <p>There are no customers in the database yet.</p>
            </div>
        <?php else: ?>
            <div class="customers-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['id']); ?></td>
                                <td class="customer-name">
                                    <?php echo htmlspecialchars(trim($customer['first_name'] . ' ' . $customer['last_name'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($customer['status']); ?>">
                                        <?php echo htmlspecialchars($customer['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="text-align: center; color: #666666; margin-top: 30px;">
                Total Customers: <strong><?php echo count($customers); ?></strong>
            </p>
        <?php endif; ?>
    </div>

<?php include 'includes/footer.php'; ?>
