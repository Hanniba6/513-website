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

// Set variables for header
$pageTitle = 'Admin Panel - Product Management';
$cssPath = '../';

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

// Include header
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Apple-style Admin Page -->
    <div class="bg-[#fbfbfd] min-h-screen py-8">
        <!-- Page Header -->
        <div class="max-w-[1400px] mx-auto px-5 mb-8">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl font-semibold text-[#1d1d1f] tracking-tight mb-2">Product Management</h1>
                    <p class="text-base text-[#86868b]">Manage your graphics card inventory</p>
                </div>
                <div class="flex gap-3">
                    <a href="../products.php" class="px-5 py-2.5 bg-[#f5f5f7] text-[#1d1d1f] rounded-full text-sm font-medium hover:bg-[#e8e8ed] transition-all">
                        View Store
                    </a>
                    <a href="?logout=1" class="px-5 py-2.5 bg-[#f5f5f7] text-[#1d1d1f] rounded-full text-sm font-medium hover:bg-[#e8e8ed] transition-all">
                        Logout
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-[1400px] mx-auto px-5">
            <!-- Message -->
            <?php if ($message): ?>
                <div class="mb-6 px-5 py-4 rounded-2xl <?php echo $messageType === 'success' ? 'bg-[#d1f4e0] text-[#0d5028]' : 'bg-[#ffd3d3] text-[#8b0000]'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Section -->
                <div class="lg:col-span-1 bg-white rounded-3xl p-8 shadow-sm border border-[#d2d2d7]">
                    <h2 class="text-2xl font-semibold text-[#1d1d1f] mb-6 tracking-tight">
                        <?php echo $editProduct ? 'Edit Product' : 'Add Product'; ?>
                    </h2>
                    <form method="POST" action="" class="space-y-5">
                        <?php if ($editProduct): ?>
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($editProduct['id']); ?>">
                        <?php endif; ?>

                        <div>
                            <label for="name" class="block text-sm font-medium text-[#1d1d1f] mb-2">Product Name *</label>
                            <input type="text" id="name" name="name" required
                                   value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="image_url" class="block text-sm font-medium text-[#1d1d1f] mb-2">Image URL</label>
                            <input type="url" id="image_url" name="image_url"
                                   value="<?php echo htmlspecialchars($editProduct['image_url'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-[#1d1d1f] mb-2">Price ($) *</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" required
                                       value="<?php echo htmlspecialchars($editProduct['price'] ?? '0'); ?>"
                                       class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="discount_percent" class="block text-sm font-medium text-[#1d1d1f] mb-2">Discount (%)</label>
                                <input type="number" id="discount_percent" name="discount_percent" min="0" max="100"
                                       value="<?php echo htmlspecialchars($editProduct['discount_percent'] ?? '0'); ?>"
                                       class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-[#1d1d1f] mb-2">Quantity</label>
                            <input type="number" id="quantity" name="quantity" min="0"
                                   value="<?php echo htmlspecialchars($editProduct['quantity'] ?? '0'); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-[#1d1d1f] mb-2">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all resize-vertical"><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-[#1d1d1f] mb-2">Category *</label>
                            <select id="category" name="category" required
                                    class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                                <option value="">Select Category</option>
                                <option value="Gaming GPUs" <?php echo (isset($editProduct['category']) && $editProduct['category'] === 'Gaming GPUs') ? 'selected' : ''; ?>>Gaming GPUs</option>
                                <option value="Professional GPUs" <?php echo (isset($editProduct['category']) && $editProduct['category'] === 'Professional GPUs') ? 'selected' : ''; ?>>Professional GPUs</option>
                            </select>
                        </div>

                        <div>
                            <label for="supplier" class="block text-sm font-medium text-[#1d1d1f] mb-2">Supplier</label>
                            <input type="text" id="supplier" name="supplier"
                                   value="<?php echo htmlspecialchars($editProduct['supplier'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="memory_size" class="block text-sm font-medium text-[#1d1d1f] mb-2">Memory Size</label>
                            <input type="text" id="memory_size" name="memory_size"
                                   value="<?php echo htmlspecialchars($editProduct['memory_size'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="power_consumption" class="block text-sm font-medium text-[#1d1d1f] mb-2">Power Consumption</label>
                            <input type="text" id="power_consumption" name="power_consumption"
                                   value="<?php echo htmlspecialchars($editProduct['power_consumption'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="origin_country" class="block text-sm font-medium text-[#1d1d1f] mb-2">Origin Country</label>
                            <input type="text" id="origin_country" name="origin_country"
                                   value="<?php echo htmlspecialchars($editProduct['origin_country'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="spice_level" class="block text-sm font-medium text-[#1d1d1f] mb-2">Tier</label>
                            <input type="text" id="spice_level" name="spice_level"
                                   value="<?php echo htmlspecialchars($editProduct['spice_level'] ?? ''); ?>"
                                   class="w-full px-4 py-2.5 bg-[#f5f5f7] border border-[#d2d2d7] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0071e3] focus:border-transparent transition-all">
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="submit" class="flex-1 px-5 py-3 bg-[#0071e3] text-white rounded-full text-sm font-medium hover:bg-[#0077ed] transition-all">
                                <?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
                            </button>
                            <?php if ($editProduct): ?>
                                <a href="index.php" class="px-5 py-3 bg-[#f5f5f7] text-[#1d1d1f] rounded-full text-sm font-medium hover:bg-[#e8e8ed] transition-all flex items-center justify-center">
                                    Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Products List Section -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-[#d2d2d7]">
                    <h2 class="text-2xl font-semibold text-[#1d1d1f] mb-6 tracking-tight">
                        All Products <span class="text-[#86868b]">(<?php echo count($products); ?>)</span>
                    </h2>
                    <?php if (empty($products)): ?>
                        <div class="text-center py-12 text-[#86868b]">
                            <p class="text-lg">No products found</p>
                            <p class="text-sm mt-2">Add your first product using the form</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-[#d2d2d7]">
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#86868b] uppercase tracking-wider">Image URL</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#86868b] uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#86868b] uppercase tracking-wider">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#86868b] uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-[#86868b] uppercase tracking-wider">Stock</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-[#86868b] uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#d2d2d7]">
                                    <?php foreach ($products as $product): ?>
                                        <tr class="hover:bg-[#f5f5f7] transition-colors">
                                            <td class="px-4 py-4">
                                                <div class="max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap text-xs text-[#2997ff] font-mono" title="<?php echo htmlspecialchars($product['image_url'] ?? 'N/A'); ?>">
                                                    <?php echo htmlspecialchars($product['image_url'] ?? 'N/A'); ?>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-sm font-medium text-[#1d1d1f]">
                                                <?php echo htmlspecialchars($product['name'] ?? 'N/A'); ?>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-[#424245]">
                                                <?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?>
                                            </td>
                                            <td class="px-4 py-4 text-sm font-semibold text-[#1d1d1f]">
                                                $<?php echo number_format(floatval($product['price'] ?? 0), 2); ?>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-[#424245]">
                                                <?php echo htmlspecialchars($product['quantity'] ?? 0); ?>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <div class="flex gap-2 justify-end">
                                                    <a href="?edit=1&id=<?php echo $product['id']; ?>"
                                                       class="px-4 py-2 bg-[#0071e3] text-white rounded-full text-xs font-medium hover:bg-[#0077ed] transition-all">
                                                        Edit
                                                    </a>
                                                    <a href="?delete=1&id=<?php echo $product['id']; ?>"
                                                       class="px-4 py-2 bg-[#ff3b30] text-white rounded-full text-xs font-medium hover:bg-[#ff2d20] transition-all"
                                                       onclick="return confirm('Are you sure you want to delete this product?');">
                                                        Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>

