-- Student Book Exchange Database Schema

CREATE DATABASE IF NOT EXISTS student_book_exchange
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE student_book_exchange;

-- Users: students and admins
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role ENUM('student','admin') NOT NULL DEFAULT 'student',
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Books (listings created by students)
CREATE TABLE books (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) NOT NULL,
  subject VARCHAR(255),
  course VARCHAR(255),
  price_cents INT UNSIGNED NOT NULL,
  `condition` ENUM('new','like_new','used','damaged') NOT NULL DEFAULT 'used',
  description TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_books_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX idx_books_subject (subject),
  INDEX idx_books_course (course),
  INDEX idx_books_title (title)
) ENGINE=InnoDB;

-- Merchandise (student-related products)
CREATE TABLE merch (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  image_url VARCHAR(500),
  price_cents INT UNSIGNED NOT NULL,
  stock_qty INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_merch_active (is_active)
) ENGINE=InnoDB;

-- Orders (header)
CREATE TABLE orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  status ENUM('pending','paid','cancelled','refunded') NOT NULL DEFAULT 'pending',
  total_cents INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (status)
) ENGINE=InnoDB;

-- Order items (normalized line items)
CREATE TABLE order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  merch_id BIGINT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price_cents INT UNSIGNED NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_items_merch FOREIGN KEY (merch_id) REFERENCES merch(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uq_order_merch (order_id, merch_id)
) ENGINE=InnoDB;

-- Reviews: can target a book OR a merch item
CREATE TABLE reviews (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  book_id BIGINT UNSIGNED NULL,
  merch_id BIGINT UNSIGNED NULL,
  rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_user  FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_reviews_book  FOREIGN KEY (book_id) REFERENCES books(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_reviews_merch FOREIGN KEY (merch_id) REFERENCES merch(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT chk_review_target CHECK (book_id IS NOT NULL OR merch_id IS NOT NULL),
  INDEX idx_reviews_user (user_id),
  INDEX idx_reviews_book (book_id),
  INDEX idx_reviews_merch (merch_id)
) ENGINE=InnoDB;

-- Favourites: users can favourite books or merch
CREATE TABLE favourites (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  book_id BIGINT UNSIGNED NULL,
  merch_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_fav_user  FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_fav_book  FOREIGN KEY (book_id) REFERENCES books(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_fav_merch FOREIGN KEY (merch_id) REFERENCES merch(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed data for demo

INSERT INTO users (role, first_name, last_name, email, password_hash) VALUES
('admin','Campus','Admin','admin@studentbookexchange.test','$2y$12$examplehash'),
('student','Danil','Hordiienko','danil@example.com','$2y$12$examplehash');

INSERT INTO books (user_id, title, author, subject, course, price_cents, `condition`) VALUES
(2,'Clean Code','Robert C. Martin','Programming','CS101',1500,'used'),
(2,'Database System Concepts','Silberschatz','Databases','CS202',2500,'like_new');

INSERT INTO merch (name, description, price_cents, stock_qty) VALUES
('Campus Hoodie','Soft cotton hoodie',3999,30),
('Student Mug','White ceramic mug',999,100);

INSERT INTO orders (user_id, status, total_cents) VALUES (2,'paid',4998);

INSERT INTO order_items (order_id, merch_id, quantity, unit_price_cents) VALUES
(1,1,1,3999),
(1,2,1,999);

INSERT INTO reviews (user_id, book_id, rating, comment) VALUES
(2,1,5,'Great book for practising clean code principles.');

INSERT INTO favourites (user_id, book_id) VALUES (2,2);
