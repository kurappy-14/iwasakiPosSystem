DROP DATABASE IF EXISTS exampledb;

CREATE DATABASE exampledb;
use exampledb;

-- テーブルの作成
-- 1. ordersテーブルの作成 (statusをINTに変更)
CREATE TABLE orders (
    order_id INT PRIMARY KEY,         -- 主キー
    call_number INT,
    payment_type VARCHAR(255),
    reference_number VARCHAR(255),
    provide_status INT,                       -- INT型に変更
    order_date DATETIME
);

-- 2. productsテーブルの作成
CREATE TABLE products (
    product_code VARCHAR(255) PRIMARY KEY, -- 主キー
    category_name VARCHAR(255),             
    product_name VARCHAR(255),
    price INT,
    stockpile INT,
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


INSERT INTO orders (order_id, call_number, payment_type, reference_number, provide_status, order_date) VALUES
(1, 101, '現金', 'REF101', 5, '2023-01-01 12:00:00'),
(2, 102, 'IC', 'REF102', 5, '2023-01-01 12:15:00'),
(3, 103, 'クレジットカード', 'REF103', 4, '2023-01-01 12:30:00'),
(4, 104, '現金', 'REF104', 4, '2023-01-01 12:45:00'),
(5, 105, 'IC', 'REF105', 3, '2023-01-01 13:00:00'),
(6, 106, '現金', 'REF106', 3, '2023-01-01 13:15:00'),
(7, 107, 'クレジットカード', 'REF107', 2, '2023-01-01 13:30:00'),
(8, 108, 'IC', 'REF108', 2, '2023-01-01 13:45:00'),
(9, 109, '現金', 'REF109', 1, '2023-01-01 14:00:00'),
(10, 110, 'クレジットカード', 'REF110', 1, '2023-01-01 14:15:00');



INSERT INTO products (product_code, category_name, product_name, price, stockpile) VALUES
('P001', 'C001', 'コーヒー', 300, 50),
('P002', 'C001', 'オレンジジュース', 250, 40),
('P003', 'C002', 'ハンバーガー', 500, 30),
('P004', 'C002', 'サンドイッチ', 450, 25);


INSERT INTO purchase (order_id, product_code, quantity) VALUES
(1, 'P001', 2),
(1, 'P003', 1),
(2, 'P002', 1),
(2, 'P004', 2),
(3, 'P001', 3),
(3, 'P003', 2),
(4, 'P004', 1),
(5, 'P002', 3),
(6, 'P001', 1),
(6, 'P004', 1),
(7, 'P003', 1),
(7, 'P002', 2),
(8, 'P004', 3),
(9, 'P001', 2),
(9, 'P002', 1),
(10, 'P003', 2),
(10, 'P001', 1);
