-- ============================================
-- 创建 customers 客户表
-- ============================================
-- 此表用于存储客户注册信息
-- 与 orders 表通过 customer_id 关联
-- ============================================

CREATE TABLE IF NOT EXISTS customers (
    -- 客户唯一ID，主键，自动递增
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    -- 客户姓名（必填）
    name VARCHAR(255) NOT NULL,

    -- 客户邮箱（必填，唯一）
    -- 用于登录和接收订单通知
    email VARCHAR(255) NOT NULL UNIQUE,

    -- 密码（加密存储，使用 bcrypt）
    password VARCHAR(255) NOT NULL,

    -- 电话号码（可选）
    phone VARCHAR(50),

    -- 地址（可选）
    address TEXT,

    -- 注册时间（自动设置为当前时间）
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- 最后登录时间（可选）
    last_login TIMESTAMP NULL,

    -- 添加邮箱索引，加快登录查询速度
    INDEX idx_email (email),

    -- 添加注册时间索引，方便按时间查询
    INDEX idx_registration_date (registration_date)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 插入测试数据（可选）
-- ============================================
-- 密码: password123（已加密）
INSERT INTO customers (name, email, password, phone, address) VALUES
('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '13800138000', '北京市朝阳区测试路123号'),
('张三', 'zhangsan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '13900139000', '上海市浦东新区测试街456号')
ON DUPLICATE KEY UPDATE name=name;

-- ============================================
-- 验证表是否创建成功
-- ============================================
-- 查看表结构
-- DESC customers;

-- 查看测试数据
-- SELECT id, name, email, phone FROM customers;

-- ============================================
-- 完成！
-- ============================================
