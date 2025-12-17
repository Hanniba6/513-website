-- ============================================
-- Add Churn Analysis Fields to Existing Tables
-- ============================================
-- This script adds necessary fields for customer churn analysis
-- ============================================

-- Add churn analysis fields to customers table
-- Note: We check and add columns individually to avoid errors if they already exist

-- Add months_as_customer column
ALTER TABLE customers
ADD COLUMN months_as_customer INT DEFAULT 0 COMMENT 'Number of months as customer';

-- Add order_count column
ALTER TABLE customers
ADD COLUMN order_count INT DEFAULT 0 COMMENT 'Total number of orders placed';

-- Add days_since_last_order column
ALTER TABLE customers
ADD COLUMN days_since_last_order INT DEFAULT NULL COMMENT 'Days since last order';

-- Add last_order_date column
ALTER TABLE customers
ADD COLUMN last_order_date TIMESTAMP NULL COMMENT 'Date of last order';

-- Add churned column
ALTER TABLE customers
ADD COLUMN churned TINYINT(1) DEFAULT 0 COMMENT 'Churn status: 0=Active, 1=Churned';

-- Add indexes
ALTER TABLE customers
ADD INDEX idx_churned (churned);

ALTER TABLE customers
ADD INDEX idx_last_order_date (last_order_date);

-- ============================================
-- Create view for easy churn analysis
-- ============================================
CREATE OR REPLACE VIEW customer_churn_analysis AS
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
-- Drop procedure if it exists
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
