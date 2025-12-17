# Hanniba Store - Administrator Manual

**Version 1.0**
**Last Updated: December 2025**

---

## Table of Contents

1. [Introduction](#introduction)
2. [System Architecture](#system-architecture)
3. [Installation & Setup](#installation--setup)
4. [Database Management](#database-management)
5. [Admin Panel Guide](#admin-panel-guide)
6. [Product Management](#product-management)
7. [Order Management](#order-management)
8. [Customer Management](#customer-management)
9. [Security & Maintenance](#security--maintenance)
10. [Troubleshooting](#troubleshooting)
11. [API Reference](#api-reference)

---

## 1. Introduction

### 1.1 Purpose

This manual provides comprehensive guidance for administrators managing the Hanniba Store e-commerce platform. It covers installation, configuration, daily operations, and troubleshooting.

### 1.2 Target Audience

- System Administrators
- Database Administrators
- Website Administrators
- Technical Support Staff

### 1.3 Prerequisites

**Technical Skills Required:**
- Basic PHP knowledge
- MySQL database administration
- Web server configuration
- Command line usage
- File system management

**System Requirements:**
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Apache/Nginx web server
- 100MB+ disk space
- SSL certificate (recommended)

---

## 2. System Architecture

### 2.1 Technology Stack

**Backend:**
- PHP 7.4+
- MySQL Database
- Session-based authentication
- JSON data storage for products

**Frontend:**
- HTML5
- Tailwind CSS (via CDN)
- Vanilla JavaScript
- Canvas API (particle effects)

**Design System:**
- Apple-inspired UI/UX
- Responsive grid layouts
- System font stack
- Custom SVG icons

### 2.2 File Structure

```
/week11/
├── admin/
│   ├── index.php              # Admin dashboard
│   ├── index_original.php     # Backup
│   └── login.php              # Admin login
├── data/
│   └── products.json          # Product database (JSON)
├── includes/
│   ├── header.php             # Global header
│   └── footer.php             # Global footer
├── about.php                  # About page
├── cart.php                   # Shopping cart
├── checkout.php               # Checkout process
├── config.php                 # Database configuration
├── login.php                  # Customer login
├── logout.php                 # Logout handler
├── order_history.php          # Customer orders
├── products.php               # Product catalog
├── register.php               # Customer registration
├── thankyou.php               # Order confirmation
├── create_customers_table.sql # Customer table SQL
├── create_orders_table.sql    # Orders table SQL
└── add_shipping_address.sql   # Address field SQL
```

### 2.3 Database Schema

**Tables:**
1. `customers` - Customer account information
2. `orders` - Order records with items and shipping

**Key Relationships:**
- orders.customer_id → customers.id (foreign key)

---

## 3. Installation & Setup

### 3.1 Server Requirements

**Minimum:**
- PHP 7.4+
- MySQL 5.7+
- 100MB disk space
- 256MB RAM

**Recommended:**
- PHP 8.0+
- MySQL 8.0+
- 500MB disk space
- 512MB RAM
- SSL certificate

### 3.2 Installation Steps

**Step 1: Upload Files**
```bash
# Upload all files to web server
# Recommended location: /var/www/html/week11/
# Or use FTP/SFTP client
```

**Step 2: Set File Permissions**
```bash
# Make writable for product management
chmod 755 /path/to/week11/
chmod 644 /path/to/week11/data/products.json
chmod 755 /path/to/week11/data/

# Secure config file
chmod 600 /path/to/week11/config.php
```

**Step 3: Configure Database Connection**

Edit `config.php`:
```php
<?php
define('DB_HOST', 'localhost');      // Your MySQL host
define('DB_USER', 'your_username');  // MySQL username
define('DB_PASS', 'your_password');  // MySQL password
define('DB_NAME', 'your_database');  // Database name

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
```

**Step 4: Create Database Tables**

```bash
# Connect to MySQL
mysql -u your_username -p your_database

# Run SQL scripts in order
mysql -u your_username -p your_database < create_customers_table.sql
mysql -u your_username -p your_database < create_orders_table.sql
mysql -u your_username -p your_database < add_shipping_address.sql
```

**Step 5: Initialize Products**

Create `data/products.json`:
```json
[]
```

Or populate with sample data:
```json
[
  {
    "id": 1,
    "name": "NVIDIA GeForce RTX 4090",
    "description": "Top-tier gaming performance",
    "price": 1599.99,
    "discount_percent": 10,
    "category": "High-End Gaming",
    "image_url": "path/to/image.jpg"
  }
]
```

**Step 6: Test Installation**

Visit:
- `http://your-domain.com/week11/products.php` - Should load
- `http://your-domain.com/week11/admin/` - Admin panel
- Check for PHP errors in browser console

### 3.3 SSL Configuration (Recommended)

**Using Let's Encrypt:**
```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-apache

# Obtain certificate
sudo certbot --apache -d your-domain.com

# Auto-renewal
sudo certbot renew --dry-run
```

**Force HTTPS in .htaccess:**
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 4. Database Management

### 4.1 Customers Table

**Schema:**
```sql
CREATE TABLE customers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);
```

**Key Fields:**
- `id`: Unique customer identifier
- `email`: Login credential (unique)
- `password_hash`: Encrypted password
- `phone`: Optional contact number

**Management Commands:**
```sql
-- View all customers
SELECT id, name, email, phone, created_at FROM customers;

-- Find customer by email
SELECT * FROM customers WHERE email = 'customer@example.com';

-- Count total customers
SELECT COUNT(*) FROM customers;

-- Delete inactive customer
DELETE FROM customers WHERE id = 123;
```

### 4.2 Orders Table

**Schema:**
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(12, 2) NOT NULL,
    order_status VARCHAR(20) NOT NULL,
    items JSON NOT NULL,
    shipping_address JSON NULL,
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_date (order_date),
    INDEX idx_order_status (order_status)
);
```

**Key Fields:**
- `items`: JSON array of order items
- `shipping_address`: JSON object with address
- `order_status`: pending, processing, shipped, delivered, cancelled

**Management Commands:**
```sql
-- View recent orders
SELECT id, customer_name, total_amount, order_status, order_date
FROM orders ORDER BY order_date DESC LIMIT 10;

-- Update order status
UPDATE orders SET order_status = 'shipped' WHERE id = 456;

-- View orders by status
SELECT * FROM orders WHERE order_status = 'pending';

-- Calculate total revenue
SELECT SUM(total_amount) FROM orders WHERE order_status != 'cancelled';

-- Orders by date range
SELECT * FROM orders
WHERE order_date BETWEEN '2025-01-01' AND '2025-12-31';
```

### 4.3 Backup & Restore

**Daily Backup Script:**
```bash
#!/bin/bash
# backup_database.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/path/to/backups"
DB_NAME="your_database"
DB_USER="your_username"
DB_PASS="your_password"

# Create backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/backup_$DATE.sql

# Compress
gzip $BACKUP_DIR/backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete

echo "Backup completed: backup_$DATE.sql.gz"
```

**Restore from Backup:**
```bash
# Decompress
gunzip backup_20251205_120000.sql.gz

# Restore
mysql -u your_username -p your_database < backup_20251205_120000.sql
```

### 4.4 Database Maintenance

**Weekly Maintenance:**
```sql
-- Optimize tables
OPTIMIZE TABLE customers, orders;

-- Check table integrity
CHECK TABLE customers, orders;

-- Repair if needed
REPAIR TABLE customers;
REPAIR TABLE orders;

-- Analyze for query optimization
ANALYZE TABLE customers, orders;
```

**Monitor Database Size:**
```sql
SELECT
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'your_database'
ORDER BY (data_length + index_length) DESC;
```

---

## 5. Admin Panel Guide

### 5.1 Accessing Admin Panel

**URL:** `http://your-domain.com/week11/admin/`

**Default Access:**
- No password protection by default
- **IMPORTANT:** Implement authentication before production

**Security Recommendation:**
Add basic authentication in `admin/index.php`:
```php
<?php
session_start();

// Simple admin authentication
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
```

### 5.2 Admin Dashboard Layout

**Header Section:**
- Page title "Product Management"
- Navigation breadcrumbs
- Logout option (if auth enabled)

**Main Content:**
- Left side: Add/Edit Product Form
- Right side: Product List Table
- Responsive: stacks vertically on mobile

**Color Scheme:**
- Background: #fbfbfd (off-white)
- Cards: #ffffff (white)
- Borders: #d2d2d7 (light gray)
- Primary button: #0071e3 (Apple blue)
- Delete button: #ff3b30 (Apple red)

### 5.3 Admin Panel Features

**Product Form Fields:**
- Product Name (text, required)
- Description (textarea, required)
- Price (number, required, min: 0.01)
- Discount % (number, 0-100)
- Category (select dropdown)
- Image URL (text, optional)

**Available Categories:**
- High-End Gaming
- Mid-Range
- Budget-Friendly
- Professional Workstations

**Product Table Columns:**
- ID
- Name
- Price
- Discount %
- Category
- Actions (Edit, Delete)

---

## 6. Product Management

### 6.1 Adding Products

**Step-by-Step:**

1. **Access Admin Panel**
   - Navigate to `/admin/index.php`

2. **Fill Product Form**
   - Enter product name
   - Write description
   - Set price (dollars and cents)
   - Add discount percentage (optional, 0-100)
   - Select category from dropdown
   - Paste image URL (optional)

3. **Submit**
   - Click blue "Add Product" button
   - Success message displays
   - Product appears in table
   - Form resets for next entry

**Example:**
```
Name: NVIDIA GeForce RTX 4080
Description: High-performance graphics card for gaming
Price: 1199.99
Discount: 15
Category: High-End Gaming
Image URL: https://example.com/images/rtx4080.jpg
```

### 6.2 Editing Products

**Process:**

1. **Locate Product**
   - Find product in table by ID or name

2. **Click Edit**
   - Click blue "Edit" button in Actions column
   - Page reloads with form populated

3. **Modify Fields**
   - Form shows current values
   - Change any field as needed
   - Form header changes to "Edit Product"

4. **Save Changes**
   - Click "Update Product" button
   - Changes saved to JSON file
   - Success message confirms update

**Tips:**
- Only edit one product at a time
- Changes are immediate
- No undo function - be careful

### 6.3 Deleting Products

**Warning:** Deletion is permanent. No recovery possible.

**Steps:**

1. **Find Product**
   - Locate in product table

2. **Delete Action**
   - Click red "Delete" button
   - Confirmation popup appears
   - Click "OK" to confirm, "Cancel" to abort

3. **Confirmation**
   - Product removed from table
   - Product removed from `products.json`
   - Success message displays

**Best Practices:**
- Backup `products.json` before bulk deletions
- Check for active orders containing product
- Consider disabling instead of deleting

### 6.4 Product Data File

**Location:** `/data/products.json`

**Structure:**
```json
[
  {
    "id": 1,
    "name": "Product Name",
    "description": "Product description text",
    "price": 999.99,
    "discount_percent": 10,
    "category": "High-End Gaming",
    "image_url": "https://example.com/image.jpg"
  }
]
```

**Manual Editing:**
```bash
# Backup first
cp data/products.json data/products.json.backup

# Edit with text editor
nano data/products.json

# Validate JSON syntax
php -r "json_decode(file_get_contents('data/products.json'));"
```

**Bulk Import:**
```php
<?php
// bulk_import.php
$products = [
    [
        "name" => "Product 1",
        "description" => "Description 1",
        "price" => 100.00,
        "discount_percent" => 0,
        "category" => "Mid-Range",
        "image_url" => ""
    ],
    // Add more...
];

// Get existing products
$existing = json_decode(file_get_contents('data/products.json'), true) ?: [];

// Get next ID
$nextId = empty($existing) ? 1 : max(array_column($existing, 'id')) + 1;

// Add IDs and merge
foreach ($products as &$product) {
    $product['id'] = $nextId++;
}

$merged = array_merge($existing, $products);

// Save
file_put_contents('data/products.json', json_encode($merged, JSON_PRETTY_PRINT));
echo "Imported " . count($products) . " products\n";
?>
```

---

## 7. Order Management

### 7.1 Viewing Orders

**Database Query:**
```sql
SELECT
    o.id,
    o.customer_name,
    o.customer_email,
    o.total_amount,
    o.order_status,
    o.order_date
FROM orders o
ORDER BY o.order_date DESC;
```

**Via phpMyAdmin:**
1. Navigate to your database
2. Click "orders" table
3. Click "Browse" tab
4. View all orders

**Export Orders:**
```sql
-- Export to CSV
SELECT * FROM orders
INTO OUTFILE '/tmp/orders.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

### 7.2 Order Status Management

**Status Values:**
- `pending` - Just placed, awaiting processing
- `processing` - Being prepared
- `shipped` - Sent to customer
- `delivered` - Successfully delivered
- `cancelled` - Cancelled/refunded

**Update Status:**
```sql
-- Single order
UPDATE orders
SET order_status = 'shipped'
WHERE id = 123;

-- Bulk update old pending
UPDATE orders
SET order_status = 'processing'
WHERE order_status = 'pending'
AND order_date < DATE_SUB(NOW(), INTERVAL 1 DAY);
```

**Status Change Script:**
```php
<?php
// update_order_status.php
require_once '../config.php';

$orderId = $_POST['order_id'];
$newStatus = $_POST['status'];

// Validate status
$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($newStatus, $validStatuses)) {
    die("Invalid status");
}

$conn = getDBConnection();
$stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$stmt->bind_param("si", $newStatus, $orderId);

if ($stmt->execute()) {
    echo "Order #{$orderId} updated to {$newStatus}";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
```

### 7.3 Order Details

**Viewing Order Items:**
```sql
-- Order with parsed JSON
SELECT
    id,
    customer_name,
    JSON_PRETTY(items) as order_items,
    JSON_PRETTY(shipping_address) as address,
    total_amount
FROM orders
WHERE id = 123;
```

**PHP Script to View:**
```php
<?php
require_once 'config.php';

$orderId = 123;
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

echo "Order #{$order['id']}\n";
echo "Customer: {$order['customer_name']}\n";
echo "Email: {$order['customer_email']}\n";
echo "Total: \${$order['total_amount']}\n\n";

$items = json_decode($order['items'], true);
foreach ($items as $item) {
    echo "- {$item['name']} x{$item['quantity']} @ \${$item['price']} = \${$item['total']}\n";
}

if ($order['shipping_address']) {
    $address = json_decode($order['shipping_address'], true);
    echo "\nShipping to:\n";
    echo "{$address['full_name']}\n";
    echo "{$address['address_line1']}\n";
    if ($address['address_line2']) echo "{$address['address_line2']}\n";
    echo "{$address['city']}, {$address['state']} {$address['postal_code']}\n";
}
?>
```

### 7.4 Order Reports

**Daily Sales Report:**
```sql
SELECT
    DATE(order_date) as date,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue
FROM orders
WHERE order_status != 'cancelled'
GROUP BY DATE(order_date)
ORDER BY date DESC
LIMIT 30;
```

**Monthly Revenue:**
```sql
SELECT
    DATE_FORMAT(order_date, '%Y-%m') as month,
    COUNT(*) as orders,
    SUM(total_amount) as revenue
FROM orders
WHERE order_status != 'cancelled'
GROUP BY DATE_FORMAT(order_date, '%Y-%m')
ORDER BY month DESC;
```

**Top Customers:**
```sql
SELECT
    customer_email,
    customer_name,
    COUNT(*) as order_count,
    SUM(total_amount) as total_spent
FROM orders
WHERE order_status != 'cancelled'
GROUP BY customer_email
ORDER BY total_spent DESC
LIMIT 10;
```

---

## 8. Customer Management

### 8.1 Viewing Customers

**List All Customers:**
```sql
SELECT id, name, email, phone, created_at
FROM customers
ORDER BY created_at DESC;
```

**Search Customer:**
```sql
-- By email
SELECT * FROM customers WHERE email LIKE '%example.com';

-- By name
SELECT * FROM customers WHERE name LIKE '%John%';

-- By ID
SELECT * FROM customers WHERE id = 123;
```

### 8.2 Customer Orders

**View Customer's Order History:**
```sql
SELECT
    o.id,
    o.order_date,
    o.total_amount,
    o.order_status
FROM orders o
WHERE o.customer_email = 'customer@example.com'
ORDER BY o.order_date DESC;
```

**Customer Statistics:**
```sql
SELECT
    c.name,
    c.email,
    COUNT(o.id) as total_orders,
    SUM(o.total_amount) as total_spent,
    MAX(o.order_date) as last_order
FROM customers c
LEFT JOIN orders o ON c.email = o.customer_email
WHERE o.order_status != 'cancelled'
GROUP BY c.id;
```

### 8.3 Customer Support

**Common Support Queries:**

**Reset Password (Manual):**
```php
<?php
// reset_password.php
require_once 'config.php';

$email = 'customer@example.com';
$newPassword = 'temporary123';
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$conn = getDBConnection();
$stmt = $conn->prepare("UPDATE customers SET password_hash = ? WHERE email = ?");
$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    echo "Password reset for $email\n";
    echo "New password: $newPassword\n";
    echo "Ask customer to change immediately\n";
} else {
    echo "Error: " . $conn->error;
}
?>
```

**Find Orders by Email:**
```php
<?php
$email = $_GET['email'];
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_email = ? ORDER BY order_date DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

while ($order = $result->fetch_assoc()) {
    echo "Order #{$order['id']} - {$order['order_date']} - \${$order['total_amount']} - {$order['order_status']}\n";
}
?>
```

---

## 9. Security & Maintenance

### 9.1 Security Best Practices

**1. Secure config.php:**
```bash
# Set proper permissions
chmod 600 config.php

# Move outside web root if possible
mv config.php ../config.php

# Update include paths
# require_once '../config.php';
```

**2. Implement Admin Authentication:**
```php
<?php
// admin/login.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded admin (change in production)
    if ($username === 'admin' && $password === 'your_secure_password') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!-- Login form here -->
```

**3. SQL Injection Prevention:**
- Always use prepared statements
- Never concatenate user input in queries
- Validate and sanitize inputs

**4. XSS Prevention:**
- Use `htmlspecialchars()` on all output
- Encode JSON data properly
- Set Content-Security-Policy headers

**5. CSRF Protection:**
```php
<?php
// Generate token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// In forms
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

// Verify on submit
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}
?>
```

### 9.2 Regular Maintenance Tasks

**Daily:**
- [ ] Monitor error logs
- [ ] Check disk space
- [ ] Review new orders
- [ ] Backup database

**Weekly:**
- [ ] Optimize database tables
- [ ] Review security logs
- [ ] Update order statuses
- [ ] Check for abandoned carts

**Monthly:**
- [ ] Software updates (PHP, MySQL)
- [ ] Security audit
- [ ] Performance review
- [ ] Backup verification

**Quarterly:**
- [ ] Full system backup
- [ ] Disaster recovery test
- [ ] User feedback review
- [ ] Feature planning

### 9.3 Log Monitoring

**Enable PHP Error Logging:**
```php
// In php.ini or config.php
error_reporting(E_ALL);
log_errors = On
error_log = /path/to/php_errors.log
```

**MySQL Query Log:**
```sql
-- Enable general query log
SET GLOBAL general_log = 'ON';
SET GLOBAL general_log_file = '/var/log/mysql/general.log';

-- Monitor slow queries
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
```

**Review Logs:**
```bash
# PHP errors
tail -f /path/to/php_errors.log

# MySQL errors
tail -f /var/log/mysql/error.log

# Apache/Nginx access
tail -f /var/log/apache2/access.log
```

### 9.4 Performance Optimization

**Database Indexing:**
```sql
-- Ensure indexes exist
SHOW INDEX FROM customers;
SHOW INDEX FROM orders;

-- Add missing indexes
CREATE INDEX idx_customer_email ON customers(email);
CREATE INDEX idx_order_customer ON orders(customer_id);
CREATE INDEX idx_order_status ON orders(order_status);
```

**Enable Caching:**
```php
// Implement opcache
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

**Optimize JSON File:**
```bash
# Minify products.json in production
php -r "echo json_encode(json_decode(file_get_contents('data/products.json')));" > data/products.min.json
```

---

## 10. Troubleshooting

### 10.1 Common Issues

**Database Connection Failed:**
```
Error: mysqli_connect(): (HY000/1045): Access denied
```
**Solutions:**
1. Verify credentials in config.php
2. Check MySQL is running: `systemctl status mysql`
3. Grant permissions: `GRANT ALL ON database.* TO 'user'@'localhost';`
4. Check firewall rules

**Products Not Saving:**
```
Error: file_put_contents(): failed to open stream
```
**Solutions:**
1. Check file permissions: `chmod 644 data/products.json`
2. Check directory permissions: `chmod 755 data/`
3. Check disk space: `df -h`
4. Verify file exists: `ls -la data/products.json`

**JSON Syntax Error:**
```
Error: json_decode() expects parameter 1 to be string
```
**Solutions:**
1. Validate JSON: `php -r "json_decode(file_get_contents('data/products.json'));"`
2. Use JSON validator online
3. Restore from backup
4. Check for trailing commas, quotes

**Session Issues:**
```
Warning: session_start(): Failed to read session data
```
**Solutions:**
1. Check session directory: `/var/lib/php/sessions`
2. Set permissions: `chmod 1733 /var/lib/php/sessions`
3. Configure in php.ini:
   ```
   session.save_path = "/tmp"
   session.gc_maxlifetime = 1440
   ```

**Blank Page (White Screen):**
**Solutions:**
1. Enable error display:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Check PHP error log
3. Verify PHP version: `php -v`
4. Check for syntax errors: `php -l file.php`

### 10.2 Error Debugging

**Enable Debug Mode:**
```php
// Add to config.php temporarily
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

**Log Debug Information:**
```php
<?php
function debug_log($message) {
    $logFile = 'debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Usage
debug_log("User attempted login: " . $email);
debug_log("Order total calculated: $" . $total);
?>
```

**Database Query Debugging:**
```php
<?php
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
?>
```

### 10.3 Recovery Procedures

**Restore Database from Backup:**
```bash
# Stop application (optional)
# Restore database
mysql -u username -p database_name < backup.sql

# Verify restoration
mysql -u username -p -e "SELECT COUNT(*) FROM orders;" database_name
```

**Restore products.json:**
```bash
# From backup
cp data/products.json.backup data/products.json

# Verify JSON syntax
php -l data/products.json
```

**Emergency Site Maintenance:**
```php
<?php
// maintenance.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>Site Under Maintenance</title>
</head>
<body>
    <h1>We'll be right back!</h1>
    <p>Our site is currently undergoing maintenance.</p>
    <p>Please check back soon.</p>
</body>
</html>
```

---

## 11. API Reference

### 11.1 Database Functions

**config.php:**
```php
<?php
// Get database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
```

### 11.2 Product Management Functions

```php
<?php
// Get all products
function getAllProducts() {
    $file = __DIR__ . '/data/products.json';
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

// Get product by ID
function getProductById($id) {
    $products = getAllProducts();
    foreach ($products as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    return null;
}

// Save products
function saveProducts($products) {
    $file = __DIR__ . '/data/products.json';
    $json = json_encode($products, JSON_PRETTY_PRINT);
    return file_put_contents($file, $json) !== false;
}

// Add product
function addProduct($productData) {
    $products = getAllProducts();
    $productData['id'] = empty($products) ? 1 : max(array_column($products, 'id')) + 1;
    $products[] = $productData;
    return saveProducts($products);
}

// Update product
function updateProduct($id, $productData) {
    $products = getAllProducts();
    foreach ($products as $key => $product) {
        if ($product['id'] == $id) {
            $products[$key] = array_merge($product, $productData);
            return saveProducts($products);
        }
    }
    return false;
}

// Delete product
function deleteProduct($id) {
    $products = getAllProducts();
    $products = array_filter($products, function($p) use ($id) {
        return $p['id'] != $id;
    });
    return saveProducts(array_values($products));
}
?>
```

### 11.3 Order Management Functions

```php
<?php
// Get customer orders
function getCustomerOrders($customerId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $row['items'] = json_decode($row['items'], true);
        $row['shipping_address'] = json_decode($row['shipping_address'], true);
        $orders[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $orders;
}

// Create order
function createOrder($orderData) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        INSERT INTO orders
        (customer_id, customer_name, customer_email, customer_phone,
         total_amount, order_status, items, shipping_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("isssdsss",
        $orderData['customer_id'],
        $orderData['customer_name'],
        $orderData['customer_email'],
        $orderData['customer_phone'],
        $orderData['total_amount'],
        $orderData['order_status'],
        $orderData['items'],
        $orderData['shipping_address']
    );

    $success = $stmt->execute();
    $orderId = $success ? $conn->insert_id : 0;

    $stmt->close();
    $conn->close();
    return $orderId;
}

// Update order status
function updateOrderStatus($orderId, $status) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}
?>
```

---

## Appendix A: SQL Scripts

### Create Customers Table
```sql
CREATE TABLE customers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Create Orders Table
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(12, 2) NOT NULL,
    order_status VARCHAR(20) NOT NULL CHECK (order_status IN (
        'pending', 'processing', 'shipped', 'delivered', 'cancelled'
    )),
    items JSON NOT NULL,
    shipping_address JSON NULL,
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_date (order_date),
    INDEX idx_order_status (order_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Appendix B: Contact Information

**Technical Support:**
- Email: support@hannibastore.com
- Phone: 1234567890
- Hours: Mon-Fri 9:00 AM - 6:00 PM

**Emergency Contact:**
- On-call: +1-555-ADMIN
- Available 24/7 for critical issues

---

*This administrator manual is confidential and intended for authorized personnel only.*
