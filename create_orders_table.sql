-- Create orders table
-- Table structure description:
--   id: Primary key, auto-increment
--   customer_id: Customer ID who placed the order
--   customer_name: Full name of the customer
--   customer_email: Customer's email address
--   customer_phone: Customer's phone number
--   order_date: Timestamp when the order was placed
--   total_amount: Total amount of the order
--   order_status: Current status of the order (pending, processing, shipped, delivered, cancelled)
--   items: JSON field containing detailed information of order items

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
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_date (order_date),
    INDEX idx_order_status (order_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

