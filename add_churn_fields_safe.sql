-- ============================================
-- Add Churn Analysis Fields to Existing Tables (SAFE VERSION)
-- ============================================
-- This script safely adds fields, skipping if they already exist
-- ============================================

-- Create a procedure to safely add columns
DROP PROCEDURE IF EXISTS safe_add_churn_columns;

DELIMITER //

CREATE PROCEDURE safe_add_churn_columns()
BEGIN
    -- Declare variables for checking column existence
    DECLARE col_exists INT;

    -- Check and add months_as_customer
    SELECT COUNT(*) INTO col_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customers'
      AND COLUMN_NAME = 'months_as_customer';

    IF col_exists = 0 THEN
        ALTER TABLE customers
        ADD COLUMN months_as_customer INT DEFAULT 0 COMMENT 'Number of months as customer';
    END IF;

    -- Check and add order_count
    SELECT COUNT(*) INTO col_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customers'
      AND COLUMN_NAME = 'order_count';

    IF col_exists = 0 THEN
        ALTER TABLE customers
        ADD COLUMN order_count INT DEFAULT 0 COMMENT 'Total number of orders placed';
    END IF;

    -- Check and add days_since_last_order
    SELECT COUNT(*) INTO col_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customers'
      AND COLUMN_NAME = 'days_since_last_order';

    IF col_exists = 0 THEN
        ALTER TABLE customers
        ADD COLUMN days_since_last_order INT DEFAULT NULL COMMENT 'Days since last order';
    END IF;

    -- Check and add last_order_date
    SELECT COUNT(*) INTO col_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customers'
      AND COLUMN_NAME = 'last_order_date';

    IF col_exists = 0 THEN
        ALTER TABLE customers
        ADD COLUMN last_order_date TIMESTAMP NULL COMMENT 'Date of last order';
    END IF;

    -- Check and add churned
    SELECT COUNT(*) INTO col_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'customers'
      AND COLUMN_NAME = 'churned';

    IF col_exists = 0 THEN
        ALTER TABLE customers
        ADD COLUMN churned TINYINT(1) DEFAULT 0 COMMENT 'Churn status: 0=Active, 1=Churned';
    END IF;

    -- Add indexes (these will fail silently if they exist in some MySQL versions)
    -- Using a separate error handler approach
    BEGIN
        DECLARE CONTINUE HANDLER FOR 1061 BEGIN END; -- Duplicate key name error
        ALTER TABLE customers ADD INDEX idx_churned (churned);
    END;

    BEGIN
        DECLARE CONTINUE HANDLER FOR 1061 BEGIN END;
        ALTER TABLE customers ADD INDEX idx_last_order_date (last_order_date);
    END;

END //

DELIMITER ;

-- Execute the procedure to add columns safely
CALL safe_add_churn_columns();

-- Clean up the procedure
DROP PROCEDURE IF EXISTS safe_add_churn_columns;

-- ============================================
-- Create view for easy churn analysis
-- ============================================
DROP VIEW IF EXISTS customer_churn_analysis;

CREATE VIEW customer_churn_analysis AS
SELECT
    c.id,
    c.email AS customer_email,
    c.name AS customer_name,
    c.registration_date,
    c.months_as_customer,
    c.order_count,
    c.last_order_date,
    c.days_since_last_order,
    c.churned,
    CASE
        WHEN c.churned = 1 THEN 'Churned'
        ELSE 'Active'
    END AS churn_status,
    CASE
        WHEN c.order_count = 0 THEN 'No Orders'
        WHEN c.days_since_last_order IS NULL THEN 'Unknown'
        WHEN c.days_since_last_order <= 30 THEN 'Very Active'
        WHEN c.days_since_last_order <= 60 THEN 'Active'
        WHEN c.days_since_last_order <= 90 THEN 'At Risk'
        ELSE 'Inactive'
    END AS activity_level
FROM customers c;

-- ============================================
-- Create stored procedure to update churn metrics
-- ============================================
DROP PROCEDURE IF EXISTS update_customer_churn_metrics;

DELIMITER //

CREATE PROCEDURE update_customer_churn_metrics()
BEGIN
    -- Update order_count and last_order_date for all customers
    UPDATE customers c
    LEFT JOIN (
        SELECT
            customer_id,
            COUNT(*) AS total_orders,
            MAX(order_date) AS last_order
        FROM orders
        GROUP BY customer_id
    ) o ON c.id = o.customer_id
    SET
        c.order_count = COALESCE(o.total_orders, 0),
        c.last_order_date = o.last_order;

    -- Calculate days_since_last_order
    UPDATE customers
    SET days_since_last_order = DATEDIFF(NOW(), last_order_date)
    WHERE last_order_date IS NOT NULL;

    -- Calculate months_as_customer
    UPDATE customers
    SET months_as_customer = TIMESTAMPDIFF(MONTH, registration_date, NOW());

    -- Update churned status (churned if no order for more than 90 days)
    UPDATE customers
    SET churned = CASE
        WHEN days_since_last_order > 90 OR order_count = 0 THEN 1
        ELSE 0
    END;
END //

DELIMITER ;

-- ============================================
-- Execute the procedure to initialize metrics
-- ============================================
CALL update_customer_churn_metrics();

-- ============================================
-- Verification Queries
-- ============================================
-- View updated customer structure
-- DESC customers;

-- View churn analysis summary
-- SELECT
--     churned,
--     COUNT(*) AS customer_count,
--     ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM customers), 2) AS percentage
-- FROM customers
-- GROUP BY churned;

-- View customer activity levels
-- SELECT
--     activity_level,
--     COUNT(*) AS customer_count
-- FROM customer_churn_analysis
-- GROUP BY activity_level
-- ORDER BY
--     CASE activity_level
--         WHEN 'Very Active' THEN 1
--         WHEN 'Active' THEN 2
--         WHEN 'At Risk' THEN 3
--         WHEN 'Inactive' THEN 4
--         WHEN 'No Orders' THEN 5
--         ELSE 6
--     END;

-- ============================================
-- Complete!
-- ============================================
