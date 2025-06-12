CREATE DATABASE IF NOT EXISTS booknest;
USE booknest;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_json_id INT NOT NULL,  -- Make sure this column is named book_json_id
  quantity INT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(50) DEFAULT 'pending',  -- e.g., pending, completed, canceled
  total_amount DECIMAL(10, 2) NOT NULL,
  payment_method VARCHAR(50),
  payment_status VARCHAR(50) DEFAULT 'unpaid',
  shipping_address TEXT,
  tracking_number VARCHAR(100),
  expected_delivery_date DATE,
  order_notes TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  book_json_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,  -- price per unit at purchase time
  FOREIGN KEY (order_id) REFERENCES orders(id)
);

