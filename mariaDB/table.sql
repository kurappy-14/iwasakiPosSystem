DROP DATABASE IF EXISTS exampledb;

CREATE DATABASE exampledb;
use exampledb;

-- テーブルの作成
-- 1. ordersテーブルの作成 (statusをINTに変更)
CREATE TABLE orders (
    order_id INT PRIMARY KEY,         -- 主キー
    reference_number VARCHAR(255),
    provide_status INT,                       -- INT型に変更
    order_date DATETIME
);

-- 2. productsテーブルの作成
CREATE TABLE products (
    product_code VARCHAR(255) PRIMARY KEY, -- 主キー
    product_name VARCHAR(255),
    price INT
);

-- 3. purchaseテーブルの作成
CREATE TABLE purchase (
    order_id INT,                     
    product_code VARCHAR(255),        -- 外部キー
    quantity INT,
    PRIMARY KEY (order_id, product_code),  -- 複合主キーを設定
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_code) REFERENCES products(product_code)
);



-- ordersテーブルへのデータ挿入 (量を倍に)
-- INSERT INTO orders (order_id, reference_number, provide_status, order_date) VALUES
-- (1, 'REF001', -1, '2024-09-30 10:00:00'),  -- 
-- (2, 'REF002', -1, '2024-09-30 10:30:00'),   -- 
-- (3, 'REF003', 5, '2024-09-30 11:00:00'),   -- 
-- (4, 'REF004', 5, '2024-09-30 11:30:00'),   -- 
-- (5, 'REF005', 4, '2024-09-30 12:00:00'),   -- 
-- (6, 'REF006', 4, '2024-09-30 12:30:00'),   -- 
-- (7, 'REF007', 3, '2024-09-30 13:00:00'),  -- 
-- (8, 'REF008', 3, '2024-09-30 13:30:00'),   -- 
-- (9, 'REF009', 2, '2024-09-30 14:00:00'),   -- 
-- (10, 'REF010', 2, '2024-09-30 14:30:00'),  -- 
-- (11, 'REF011', 1, '2024-09-30 15:00:00'),  -- 
-- (12, 'REF012', 1, '2024-09-30 15:30:00');  -- 

-- productsテーブルへのデータ挿入
INSERT INTO products (product_code, product_name, price) VALUES
('P001', 'Pizza', 1200),
('P002', 'Pasta', 1000),
('P003', 'Salad', 800),
('P004', 'Soup', 600),
('P005', 'Dessert', 500),
('G001', '餃子', 150),
('G002', '生茶', 50),
('G003', 'カルピスウォーター', 50),
('G004', 'キリンレモン', 50);

-- purchaseテーブルへのデータ挿入（全ての注文に2種類以上の商品が含まれるように）
-- INSERT INTO purchase (order_id, product_code, quantity) VALUES
-- (1, 'P001', 2),  -- Pizza
-- (1, 'P003', 1),  -- Salad
-- (2, 'P002', 1),  -- Pasta
-- (2, 'P004', 2),  -- Soup
-- (3, 'P003', 3),  -- Salad
-- (3, 'P005', 1),  -- Dessert
-- (4, 'P001', 1),  -- Pizza
-- (4, 'P002', 1),  -- Pasta
-- (5, 'P004', 2),  -- Soup
-- (5, 'P005', 2),  -- Dessert
-- (6, 'P001', 1),  -- Pizza
-- (6, 'P003', 1),  -- Salad
-- (7, 'P002', 2),  -- Pasta
-- (7, 'P005', 1),  -- Dessert
-- (8, 'P001', 3),  -- Pizza
-- (8, 'P004', 1),  -- Soup
-- (9, 'P003', 2),  -- Salad
-- (9, 'P005', 2),  -- Dessert
-- (10, 'P002', 1), -- Pasta
-- (10, 'P003', 1), -- Salad
-- (11, 'P001', 2), -- Pizza
-- (11, 'P004', 3), -- Soup
-- (12, 'P002', 1), -- Pasta
-- (12, 'P005', 1); -- Dessert
