# Hanniba Store - Frequently Asked Questions (FAQ)

**Version 1.0 | Last Updated: December 2025**

---

## Table of Contents

**For Users:**
1. [Account & Login](#account--login)
2. [Shopping & Products](#shopping--products)
3. [Cart & Checkout](#cart--checkout)
4. [Orders & Shipping](#orders--shipping)
5. [Payment & Pricing](#payment--pricing)
6. [Technical Issues](#technical-issues)

**For Administrators:**
7. [Installation & Setup](#installation--setup)
8. [Product Management](#product-management)
9. [Order Management](#order-management)
10. [Database & Backup](#database--backup)
11. [Security & Performance](#security--performance)

---

## For Users

### Account & Login

**Q: How do I create an account?**

A: Click "Register" in the top navigation, fill in your name, email, phone, and password, then click "Register". You'll be automatically logged in.

**Q: I forgot my password. How do I reset it?**

A: Contact customer support at company@email.com with your registered email address. An administrator will assist you with password reset.

**Q: Can I change my email address?**

A: Currently, email addresses cannot be changed directly. Please contact customer support for assistance.

**Q: Why can't I login?**

A: Common reasons:
- Incorrect email or password (check spelling and caps lock)
- Account doesn't exist yet (register first)
- Browser cookies disabled (enable cookies)
- Session expired (try clearing browser cache)

**Q: Is my personal information secure?**

A: Yes! We use:
- Password hashing (bcrypt)
- Secure session management
- SQL injection prevention
- XSS protection
- HTTPS encryption (recommended)

**Q: How do I logout?**

A: Click your name/email in the top right corner, then select "Logout".

---

### Shopping & Products

**Q: How do I find specific products?**

A: Use the category filter buttons at the top of the products page. Categories include:
- High-End Gaming
- Mid-Range
- Budget-Friendly
- Professional Workstations

**Q: What do the discount badges mean?**

A: Red badges show the percentage discount off the original price. The displayed price already includes the discount.

**Q: Can I see product specifications?**

A: Product descriptions are shown on each card. For detailed specs, check the full product description or contact support.

**Q: Are the product images accurate?**

A: Yes, all product images represent the actual items. However, colors may vary slightly due to monitor settings.

**Q: What if a product is out of stock?**

A: Currently, stock levels aren't displayed. If a product is out of stock, you'll be notified after placing an order.

**Q: Can I request a product not listed?**

A: Yes! Contact customer support with your request. We're always expanding our catalog.

---

### Cart & Checkout

**Q: How do I add items to my cart?**

A: Click the blue "Add to Cart" button on any product card. The cart icon will update with the item count.

**Q: Can I change quantities in my cart?**

A: Yes! In your cart, change the quantity number and click the blue "Update" button next to each item.

**Q: How do I remove an item from my cart?**

A: Click the red "Remove" button next to the item you want to delete.

**Q: Can I save my cart for later?**

A: Cart items persist during your active session. If you logout or session expires, cart will be cleared.

**Q: Why is my cart empty?**

A: This happens if:
- You logged out
- Session expired (inactive too long)
- You cleared the cart intentionally
- You completed checkout (cart clears after order)

**Q: What payment methods do you accept?**

A: Currently, checkout collects shipping information. Payment processing setup is pending - contact support for current payment options.

**Q: Is there a minimum order amount?**

A: No, you can order any amount.

**Q: Can I use coupon codes?**

A: Coupon functionality is not currently available. Product discounts are automatically applied.

---

### Orders & Shipping

**Q: How do I track my order?**

A: Go to "Order History" from your account menu. You'll see your order status:
- **Pending**: Order received, awaiting processing
- **Processing**: Being prepared for shipment
- **Shipped**: On the way to you
- **Delivered**: Successfully delivered
- **Cancelled**: Order cancelled

**Q: Can I change my shipping address after ordering?**

A: Contact customer support immediately if you need to change the address. We'll try to update it if order hasn't shipped.

**Q: How long does shipping take?**

A: Shipping times vary by location. Contact support with your order number for specific estimates.

**Q: Do you ship internationally?**

A: Yes! Fill in your country during checkout. Shipping times and costs vary by destination.

**Q: What if my order arrives damaged?**

A: Contact customer support immediately with photos of the damage. We'll arrange replacement or refund.

**Q: Can I cancel my order?**

A: Contact support as soon as possible. Orders can be cancelled if they haven't been processed yet (status: pending).

**Q: I didn't receive my order confirmation. What should I do?**

A: Check your spam/junk folder. If not there, check "Order History" in your account. Contact support if you still can't find it.

---

### Payment & Pricing

**Q: Why is tax added to my order?**

A: A 10% tax is automatically calculated and added to your subtotal at checkout.

**Q: Are prices in USD?**

A: Yes, all prices are displayed in US Dollars ($).

**Q: Do discounted items have the same quality?**

A: Absolutely! Discounts are promotional and don't affect product quality.

**Q: Can I get a bulk discount?**

A: For bulk orders, contact customer support for potential volume discounts.

**Q: Is shipping free?**

A: Shipping costs depend on your location and order. Contact support for specific shipping rates.

---

### Technical Issues

**Q: The website is loading slowly. What can I do?**

A: Try:
- Refresh the page (F5)
- Clear browser cache
- Check your internet connection
- Try a different browser
- Disable browser extensions temporarily

**Q: Product images aren't loading. Why?**

A: This could be due to:
- Slow internet connection (wait a moment)
- Browser cache issues (clear cache)
- Image hosting temporarily down (refresh later)

**Q: The particle effects aren't working. Is something broken?**

A: Particle effects require JavaScript. Ensure:
- JavaScript is enabled in your browser
- You're using a modern browser (Chrome, Firefox, Safari, Edge)
- Your device has sufficient resources

**Q: I'm getting an error message. What should I do?**

A: Take a screenshot of the error and contact customer support with:
- What you were trying to do
- The error message
- Your browser and operating system

**Q: Is the website mobile-friendly?**

A: Yes! The site is fully responsive and optimized for mobile devices, tablets, and desktops.

**Q: Which browsers are supported?**

A: We support:
- Google Chrome (latest)
- Mozilla Firefox (latest)
- Apple Safari (latest)
- Microsoft Edge (latest)

Internet Explorer is NOT supported.

---

## For Administrators

### Installation & Setup

**Q: What are the minimum server requirements?**

A:
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server
- 100MB disk space
- 256MB RAM

**Q: How do I install the system?**

A: Follow these steps:
1. Upload files to web server
2. Set file permissions (755 for directories, 644 for files)
3. Configure `config.php` with database credentials
4. Run SQL scripts to create tables
5. Initialize `products.json`
6. Test installation

See [Admin Manual - Installation](#) for detailed guide.

**Q: Do I need SSL/HTTPS?**

A: Highly recommended but not required. SSL encrypts data transmission and builds customer trust.

**Q: Can I install this on shared hosting?**

A: Yes, as long as the hosting meets PHP and MySQL requirements.

**Q: How do I set up automatic backups?**

A: Create a cron job:
```bash
0 2 * * * /path/to/backup_script.sh
```
This runs daily at 2 AM. See Admin Manual for backup script.

---

### Product Management

**Q: Where are products stored?**

A: Products are stored in `/data/products.json` as JSON data.

**Q: How many products can I add?**

A: No hard limit, but for performance, keep under 1000 products in JSON file. For larger catalogs, consider migrating to database storage.

**Q: Can I import products in bulk?**

A: Yes, via PHP script or by manually editing `products.json`. See Admin Manual for bulk import example.

**Q: What image formats are supported?**

A: Any format displayable in browsers (JPG, PNG, WebP, GIF). We recommend JPG for photos, PNG for graphics.

**Q: Where should I host product images?**

A: Options:
- Same server (`/images/` directory)
- CDN (Cloudflare, AWS CloudFront)
- External image hosting (Imgur, etc.)

**Q: Can I have products in multiple categories?**

A: Not currently. Each product belongs to one category. You could duplicate products for multiple categories.

**Q: What happens to orders if I delete a product?**

A: Past orders retain product information (stored in order items JSON). Deleting a product only removes it from the catalog.

**Q: Can I schedule product launches or sales?**

A: Not built-in currently. You would need to manually update products at launch time.

---

### Order Management

**Q: How do I view all orders?**

A: Use SQL query:
```sql
SELECT * FROM orders ORDER BY order_date DESC;
```
Or use phpMyAdmin to browse the orders table.

**Q: How do I change an order status?**

A: Use SQL:
```sql
UPDATE orders SET order_status = 'shipped' WHERE id = 123;
```

**Q: Can customers see when I update their order status?**

A: Yes, they can see status changes in their Order History page.

**Q: What should I do with cancelled orders?**

A: Set status to 'cancelled'. They remain in database for record-keeping but aren't counted in revenue reports.

**Q: How do I handle refunds?**

A: Process refund through your payment processor, then update order status to 'cancelled' in database.

**Q: Can I export orders to CSV?**

A: Yes, use SQL:
```sql
SELECT * FROM orders INTO OUTFILE '/tmp/orders.csv'
FIELDS TERMINATED BY ',' ENCLOSED BY '"';
```

**Q: How do I print invoices?**

A: Invoicing is not built-in. You can create a PHP script to generate printable invoices from order data.

---

### Database & Backup

**Q: How often should I backup the database?**

A:
- Daily: Automated backups
- Before major changes: Manual backup
- Weekly: Verify backup integrity

**Q: What should I include in backups?**

A:
- MySQL database (all tables)
- `products.json` file
- `config.php` (credentials)
- Uploaded images (if hosted locally)

**Q: How long should I keep old backups?**

A: Recommended retention:
- Daily backups: 7 days
- Weekly backups: 4 weeks
- Monthly backups: 12 months

**Q: How do I restore from backup?**

A:
```bash
mysql -u username -p database < backup.sql
cp backup_products.json data/products.json
```

**Q: Can I migrate to a different server?**

A: Yes:
1. Backup everything
2. Install on new server
3. Restore database and files
4. Update `config.php` with new credentials
5. Test thoroughly

**Q: Should I optimize database tables?**

A: Yes, weekly:
```sql
OPTIMIZE TABLE customers, orders;
```

---

### Security & Performance

**Q: Is the admin panel secure by default?**

A: **NO!** Admin panel has no authentication by default. You MUST implement login before production.

**Q: How do I secure the admin panel?**

A: Implement session-based authentication:
```php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
```

**Q: How do I prevent SQL injection?**

A: Always use prepared statements (already implemented in the code):
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
```

**Q: Should I use HTTPS?**

A: Absolutely! HTTPS encrypts all data transmission. Use Let's Encrypt for free SSL certificates.

**Q: How do I protect config.php?**

A:
```bash
chmod 600 config.php  # Restrict permissions
# Move outside web root if possible
```

**Q: What about file upload security?**

A: Current system uses URLs, not uploads. If you add uploads:
- Validate file types
- Check file size
- Rename uploaded files
- Store outside web root if possible

**Q: How can I improve performance?**

A:
- Enable PHP OpCache
- Use CDN for images
- Minimize products.json file
- Add database indexes
- Enable GZIP compression
- Use caching headers

**Q: Should I enable error display in production?**

A: **NO!** Set in `config.php`:
```php
ini_set('display_errors', 0);
error_reporting(0);
log_errors = On;
```

**Q: How do I monitor security?**

A:
- Review PHP error logs daily
- Monitor failed login attempts
- Check database for unusual queries
- Watch for sudden traffic spikes
- Update PHP and MySQL regularly

---

## Common Error Messages

### "Connection refused" or "Database connection failed"

**Cause:** Cannot connect to MySQL server

**Solution:**
1. Check MySQL is running: `systemctl status mysql`
2. Verify credentials in `config.php`
3. Check firewall rules
4. Ensure MySQL port (3306) is open

---

### "file_put_contents(): failed to open stream"

**Cause:** Cannot write to `products.json`

**Solution:**
1. Check file permissions: `chmod 644 data/products.json`
2. Check directory permissions: `chmod 755 data/`
3. Check disk space: `df -h`

---

### "Unexpected token in JSON"

**Cause:** Syntax error in `products.json`

**Solution:**
1. Validate JSON: Use online JSON validator
2. Restore from backup
3. Check for trailing commas or missing quotes

---

### "Session could not be started"

**Cause:** Session directory not writable

**Solution:**
1. Check permissions: `chmod 1733 /var/lib/php/sessions`
2. Or set custom session path in `php.ini`

---

### "Headers already sent"

**Cause:** Output before header() call

**Solution:**
1. Remove any whitespace before `<?php`
2. Check for echo/print before header()
3. Use output buffering if needed

---

## Getting Help

### For Users

**Customer Support:**
- Email: company@email.com
- Phone: 1234567890
- Business Hours: Mon-Fri 9AM-6PM, Sat 10AM-4PM

**What to include when contacting support:**
- Your account email
- Order number (if applicable)
- Description of the issue
- Screenshots (if relevant)
- Browser and operating system

### For Administrators

**Technical Support:**
- Email: support@hannibastore.com
- Emergency: +1-555-ADMIN (24/7)

**What to include:**
- Server environment (PHP version, MySQL version)
- Error messages (complete text)
- What you were doing when error occurred
- Steps you've already tried
- Relevant log files

---

## Additional Resources

- **User Manual**: Complete guide for customers
- **Admin Manual**: Full administrative documentation
- **Quick Start Guide**: Condensed reference
- **Changelog**: Version history and updates

---

**Still have questions?**

If your question isn't answered here:
- Check the appropriate manual (User or Admin)
- Contact support with your specific question
- Visit our website for updates

---

*FAQ document updated regularly. Check back for new questions and answers.*

**Version History:**
- v1.0 (December 2025): Initial release
