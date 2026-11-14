-- Galleta Cafe - import in phpMyAdmin (creates pink_cafe_db)
CREATE DATABASE IF NOT EXISTS pink_cafe_db CHARACTER SET utf8mb4; USE pink_cafe_db;
SET FOREIGN_KEY_CHECKS=0; DROP TABLE IF EXISTS activity_logs,payments,order_items,orders,products,categories,cafe_settings,users; SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(150) NOT NULL UNIQUE,phone VARCHAR(30),password VARCHAR(255) NOT NULL,role ENUM('customer','server','admin') NOT NULL DEFAULT 'customer',active TINYINT(1) NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(role)) ENGINE=InnoDB;
CREATE TABLE categories(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80) NOT NULL,image VARCHAR(255),sort_order INT DEFAULT 0) ENGINE=InnoDB;
CREATE TABLE products(id INT AUTO_INCREMENT PRIMARY KEY,category_id INT NOT NULL,name VARCHAR(120) NOT NULL,description TEXT,price DECIMAL(10,2) NOT NULL,image VARCHAR(255),available TINYINT(1) DEFAULT 1,featured TINYINT(1) DEFAULT 0,best_seller TINYINT(1) DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(category_id) REFERENCES categories(id),INDEX(category_id)) ENGINE=InnoDB;
CREATE TABLE orders(id INT AUTO_INCREMENT PRIMARY KEY,order_no VARCHAR(20) NOT NULL UNIQUE,customer_id INT NOT NULL,server_id INT NULL,order_type ENUM('dine-in','takeaway') DEFAULT 'dine-in',subtotal DECIMAL(10,2),discount DECIMAL(10,2) DEFAULT 0,service_charge DECIMAL(10,2) DEFAULT 0,tax DECIMAL(10,2) DEFAULT 0,total DECIMAL(10,2) NOT NULL,payment_method VARCHAR(30),payment_status ENUM('unpaid','paid') DEFAULT 'unpaid',status ENUM('pending','confirmed','preparing','ready','completed','cancelled') DEFAULT 'pending',notes VARCHAR(300),reject_reason VARCHAR(200),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(customer_id) REFERENCES users(id),FOREIGN KEY(server_id) REFERENCES users(id),INDEX(status),INDEX(created_at)) ENGINE=InnoDB;
CREATE TABLE order_items(id INT AUTO_INCREMENT PRIMARY KEY,order_id INT NOT NULL,product_id INT NOT NULL,product_name VARCHAR(120),price DECIMAL(10,2) NOT NULL,qty INT NOT NULL,FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id)) ENGINE=InnoDB;
CREATE TABLE payments(id INT AUTO_INCREMENT PRIMARY KEY,order_id INT NOT NULL,method VARCHAR(30),amount DECIMAL(10,2),paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE cafe_settings(k VARCHAR(50) PRIMARY KEY,v TEXT) ENGINE=InnoDB;
CREATE TABLE activity_logs(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,action VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(user_id)) ENGINE=InnoDB;
INSERT INTO cafe_settings VALUES('cafe_name','Galleta Café'),('tagline','Fresh coffee, sweet bites, cozy moments.'),('currency','PHP'),('payment_methods','Cash,GCash,Card'),('tax_rate','0'),('service_rate','0');
INSERT INTO users(name,email,phone,password,role) VALUES('Admin','admin@galleta.cafe','0900000001','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),('Server Sam','server@galleta.cafe','0900000002','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','server'),('Maria Cruz','customer@galleta.cafe','0900000003','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','customer');
INSERT INTO categories(name,sort_order) VALUES ('Coffee',1),('Non-Coffee',2),('Milk Tea',3),('Frappes',4),('Pastries',5),('Cakes and Desserts',6),('Meals',7),('Snacks',8);
INSERT INTO products(category_id,name,description,price,featured,best_seller) VALUES
(1,'Spanish Latte','Freshly made spanish latte from our kitchen.',140,1,1),
(1,'Caramel Macchiato','Freshly made caramel macchiato from our kitchen.',150,0,1),
(1,'Iced Americano','Freshly made iced americano from our kitchen.',110,0,0),
(1,'Cafe Latte','Freshly made cafe latte from our kitchen.',130,0,0),
(1,'Vanilla Latte','Freshly made vanilla latte from our kitchen.',145,0,0),
(1,'Cappuccino','Freshly made cappuccino from our kitchen.',130,0,0),
(2,'Matcha Latte','Freshly made matcha latte from our kitchen.',150,1,0),
(2,'Strawberry Milk','Freshly made strawberry milk from our kitchen.',135,0,0),
(2,'Chocolate Milk','Freshly made chocolate milk from our kitchen.',120,0,0),
(2,'Blueberry Soda','Freshly made blueberry soda from our kitchen.',110,0,0),
(3,'Classic Milk Tea','Freshly made classic milk tea from our kitchen.',110,0,1),
(3,'Wintermelon Milk Tea','Freshly made wintermelon milk tea from our kitchen.',120,0,0),
(3,'Okinawa Milk Tea','Freshly made okinawa milk tea from our kitchen.',125,0,0),
(3,'Brown Sugar Milk Tea','Freshly made brown sugar milk tea from our kitchen.',140,1,0),
(4,'Java Chip Frappe','Freshly made java chip frappe from our kitchen.',165,0,1),
(4,'Strawberry Frappe','Freshly made strawberry frappe from our kitchen.',155,0,0),
(4,'Cookies and Cream Frappe','Freshly made cookies and cream frappe from our kitchen.',160,0,0),
(5,'Butter Croissant','Freshly made butter croissant from our kitchen.',95,1,0),
(5,'Chocolate Croissant','Freshly made chocolate croissant from our kitchen.',110,0,0),
(5,'Cinnamon Roll','Freshly made cinnamon roll from our kitchen.',105,0,1),
(6,'Strawberry Cheesecake','Freshly made strawberry cheesecake from our kitchen.',150,1,0),
(6,'Chocolate Cake','Freshly made chocolate cake from our kitchen.',140,0,0),
(6,'Blueberry Cheesecake','Freshly made blueberry cheesecake from our kitchen.',155,0,0),
(7,'Chicken Alfredo','Freshly made chicken alfredo from our kitchen.',190,0,0),
(7,'Carbonara','Freshly made carbonara from our kitchen.',180,0,0),
(7,'Chicken Sandwich','Freshly made chicken sandwich from our kitchen.',145,0,0),
(8,'French Fries','Freshly made french fries from our kitchen.',90,0,0),
(8,'Nachos','Freshly made nachos from our kitchen.',120,0,0),
(8,'Mozzarella Sticks','Freshly made mozzarella sticks from our kitchen.',130,0,0);
INSERT INTO orders(order_no,customer_id,server_id,order_type,subtotal,total,payment_method,payment_status,status,created_at) VALUES
('GC-DEMO-1',3,2,'dine-in',290,290,'Cash','paid','completed',NOW()-INTERVAL 3 DAY),('GC-DEMO-2',3,2,'takeaway',205,205,'GCash','paid','completed',NOW()-INTERVAL 2 DAY),
('GC-DEMO-3',3,2,'dine-in',340,340,'Card','paid','completed',NOW()-INTERVAL 1 DAY),('GC-DEMO-4',3,NULL,'dine-in',150,150,'Cash','unpaid','pending',NOW()),('GC-DEMO-5',3,2,'takeaway',260,260,'GCash','unpaid','preparing',NOW());
INSERT INTO order_items(order_id,product_id,product_name,price,qty) VALUES (1,1,'Spanish Latte',140,1),(1,2,'Caramel Macchiato',150,1),(2,19,'Butter Croissant',95,1),(2,11,'Classic Milk Tea',110,1),(3,26,'Chicken Alfredo',190,1),(3,23,'Strawberry Cheesecake',150,1),(4,2,'Caramel Macchiato',150,1),(5,1,'Spanish Latte',140,1),(5,12,'Wintermelon Milk Tea',120,1);
