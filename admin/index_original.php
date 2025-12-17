<?php
/**
 * Admin Panel - Product Management CRUD
 * Student Name: hanniba
 * Student ID: hanniba
 * 
 * Complete CRUD interface for managing graphics cards products
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// File paths
$productsFile = __DIR__ . '/../data/products.json';

// Load products
function loadProducts($file) {
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    $products = json_decode($data, true);
    return $products === null ? [] : $products;
}

// Save products
function saveProducts($file, $products) {
    $json = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    // Ensure directory exists
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($file, $json) !== false;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $products = loadProducts($productsFile);
    $id = intval($_GET['id']);
    $products = array_filter($products, function($p) use ($id) {
        return intval($p['id']) !== $id;
    });
    $products = array_values($products); // Re-index array
    saveProducts($productsFile, $products);
    header('Location: index.php?msg=deleted');
    exit;
}

// Handle form submission (add/edit)
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products = loadProducts($productsFile);
    
    // Sanitize and validate input
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
    $name = trim($_POST['name'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $discount_percent = intval($_POST['discount_percent'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $supplier = trim($_POST['supplier'] ?? '');
    $memory_size = trim($_POST['memory_size'] ?? '');
    $power_consumption = trim($_POST['power_consumption'] ?? '');
    $origin_country = trim($_POST['origin_country'] ?? '');
    $spice_level = trim($_POST['spice_level'] ?? '');

    // Validation
    if (empty($name) || empty($category) || $price <= 0) {
        $message = 'Please fill in all required fields (name, category, price).';
        $messageType = 'error';
    } else {
        $product = [
            'id' => $id ?? (count($products) > 0 ? max(array_column($products, 'id')) + 1 : 1),
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'image_url' => filter_var($image_url, FILTER_SANITIZE_URL),
            'price' => $price,
            'discount_percent' => max(0, min(100, $discount_percent)),
            'quantity' => max(0, $quantity),
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars($category, ENT_QUOTES, 'UTF-8'),
            'supplier' => htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8'),
            'memory_size' => htmlspecialchars($memory_size, ENT_QUOTES, 'UTF-8'),
            'power_consumption' => htmlspecialchars($power_consumption, ENT_QUOTES, 'UTF-8'),
            'origin_country' => htmlspecialchars($origin_country, ENT_QUOTES, 'UTF-8'),
            'spice_level' => htmlspecialchars($spice_level, ENT_QUOTES, 'UTF-8')
        ];

        if ($id !== null) {
            // Edit existing product
            $found = false;
            foreach ($products as $key => $p) {
                if (intval($p['id']) === $id) {
                    $products[$key] = $product;
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $message = 'Product updated successfully!';
            } else {
                $message = 'Product not found.';
                $messageType = 'error';
            }
        } else {
            // Add new product
            $products[] = $product;
            $message = 'Product added successfully!';
        }

        if ($messageType === 'success') {
            saveProducts($productsFile, $products);
        }
    }
}

// Get message from URL
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $message = 'Product deleted successfully!';
    }
}

// Load products for display
$products = loadProducts($productsFile);

// Get product for editing
$editProduct = null;
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $editId = intval($_GET['id']);
    foreach ($products as $p) {
        if (intval($p['id']) === $editId) {
            $editProduct = $p;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Product Management</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .h-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        .admin-header {
            background-color: #fff;
            padding: 20px 0;
            border-bottom: 1px solid #eeeeee;
            margin-bottom: 30px;
        }

        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .admin-header h1 {
            font-size: 2em;
            font-weight: 800;
            color: #333;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9em;
        }

        .btn-primary {
            background-color: #21759b;
            color: white;
        }

        .btn-primary:hover {
            background-color: #185a7a;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .message {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .admin-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .form-section, .products-section {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .form-section h2, .products-section h2 {
            font-size: 1.5em;
            font-weight: 800;
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #eeeeee;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #21759b;
        }

        .form-actions {
            display: flex;
            gap: 10px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eeeeee;
        }

        .products-table th {
            background-color: #f1f1f1;
            font-weight: 600;
            color: #333;
        }

        .products-table tr:hover {
            background-color: #f9f9f9;
        }

        .product-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 0.85em;
        }

        @media (max-width: 1024px) {
            .admin-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .products-table {
                font-size: 0.85em;
            }

            .products-table th,
            .products-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="h-container">
            <div class="admin-header-content">
                <h1>Product Management Panel</h1>
                <div class="header-actions">
                    <a href="../products.php" class="btn btn-secondary">View Products</a>
                    <a href="?logout=1" class="btn btn-secondary">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="h-container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="form-section">
                <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
                <form method="POST" action="">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($editProduct['id']); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required
                               value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="image_url">Image URL</label>
                        <input type="url" id="image_url" name="image_url"
                               value="<?php echo htmlspecialchars($editProduct['image_url'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required
                               value="<?php echo htmlspecialchars($editProduct['price'] ?? '0'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="discount_percent">Discount (%)</label>
                        <input type="number" id="discount_percent" name="discount_percent" min="0" max="100"
                               value="<?php echo htmlspecialchars($editProduct['discount_percent'] ?? '0'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" min="0"
                               value="<?php echo htmlspecialchars($editProduct['quantity'] ?? '0'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Gaming GPUs" <?php echo (isset($editProduct['category']) && $editProduct['category'] === 'Gaming GPUs') ? 'selected' : ''; ?>>Gaming GPUs</option>
                            <option value="Professional GPUs" <?php echo (isset($editProduct['category']) && $editProduct['category'] === 'Professional GPUs') ? 'selected' : ''; ?>>Professional GPUs</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="supplier">Supplier</label>
                        <input type="text" id="supplier" name="supplier"
                               value="<?php echo htmlspecialchars($editProduct['supplier'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="memory_size">Memory Size</label>
                        <input type="text" id="memory_size" name="memory_size"
                               value="<?php echo htmlspecialchars($editProduct['memory_size'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="power_consumption">Power Consumption</label>
                        <input type="text" id="power_consumption" name="power_consumption"
                               value="<?php echo htmlspecialchars($editProduct['power_consumption'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="origin_country">Origin Country</label>
                        <input type="text" id="origin_country" name="origin_country"
                               value="<?php echo htmlspecialchars($editProduct['origin_country'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="spice_level">Tier</label>
                        <input type="text" id="spice_level" name="spice_level"
                               value="<?php echo htmlspecialchars($editProduct['spice_level'] ?? ''); ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
                        </button>
                        <?php if ($editProduct): ?>
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="products-section">
                <h2>All Products (<?php echo count($products); ?>)</h2>
                <?php if (empty($products)): ?>
                    <p>No products found. Add your first product using the form on the left.</p>
                <?php else: ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name'] ?? ''); ?>"
                                             class="product-image-small"
                                             onerror="this.src='https://via.placeholder.com/60x60?text=GPU'">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                    <td>$<?php echo number_format(floatval($product['price'] ?? 0), 2); ?></td>
                                    <td><?php echo htmlspecialchars($product['quantity'] ?? 0); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=1&id=<?php echo $product['id']; ?>" class="btn btn-success">Edit</a>
                                            <a href="?delete=1&id=<?php echo $product['id']; ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

