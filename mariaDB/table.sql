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

INSERT INTO orders (order_id, reference_number, provide_status, order_date) 
VALUES 
(1, 'REF12345', 2, '2024-09-01 10:00:00'),
(2, 'REF12346', 1, '2024-09-02 12:30:00'),
(3, 'REF12347', 1, '2024-09-03 14:00:00'),
(4, 'REF12347', 1, '2024-09-06 14:00:00');

INSERT INTO products (product_code, product_name, price) 
VALUES 
('P001', 'Product A', 1000),
('P002', 'Product B', 1500),
('P003', 'Product C', 2000),
('G001', '餃子', 150),
('G002', '生茶', 50),
('G003', 'カルピスウォーター', 50),
('G004', 'キリンレモン', 50);

INSERT INTO purchase (order_id, product_code, quantity) 
VALUES 
(1, 'P001', 2),
(1, 'P002', 1),
(2, 'P003', 3),
(3, 'P001', 1),
(3, 'P003', 2),
(4, 'G001', 1),
(4, 'G002', 1),
(4, 'G003', 1),
(4, 'G004', 1);
