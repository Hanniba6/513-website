-- Add shipping_address column to orders table
-- This field will store shipping address information as JSON

ALTER TABLE orders
ADD COLUMN shipping_address JSON NULL AFTER items;
