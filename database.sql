-- Database: futsal71_db
CREATE DATABASE IF NOT EXISTS futsal71_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE futsal71_db;

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    google_id VARCHAR(100) NULL,
    avatar_url VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    password VARCHAR(255) NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: fields (lapangan)
CREATE TABLE fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL,
    description TEXT,
    photo_url VARCHAR(255),
    facilities TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: bookings
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    field_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    total_hours INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (field_id) REFERENCES fields(id) ON DELETE CASCADE
);

-- Table: payments
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'success', 'expired', 'failed') DEFAULT 'pending',
    transaction_id VARCHAR(100) NULL,
    ipaymu_reference VARCHAR(100) NULL,
    payment_url TEXT NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Table: testimonials
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin Futsal 71', 'admin@futsal71.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2UheHEEmuMi', 'admin');

-- Insert sample fields
INSERT INTO fields (name, type, price_per_hour, description, photo_url, facilities, status) VALUES
('Lapangan Reguler 1', 'Vinyl', 150000.00, 'Lapangan futsal standar dengan rumput vinyl berkualitas', '/assets/images/field1.jpg', 'Rumput Vinyl, Lampu LED, Ruang Ganti, Toilet, Parkir Luas', 'active'),
('Lapangan Reguler 2', 'Vinyl', 150000.00, 'Lapangan futsal dengan spesifikasi sama dengan Lapangan 1', '/assets/images/field2.jpg', 'Rumput Vinyl, Lampu LED, Ruang Ganti, Toilet, Parkir Luas', 'active'),
('Lapangan Premium', 'Rumput Sintetis', 200000.00, 'Lapangan premium dengan rumput sintetis berkualitas tinggi', '/assets/images/field3.jpg', 'Rumput Sintetis Premium, Lampu LED, Ruang Ganti VIP, Toilet, Parkir, Minuman Gratis', 'active');

-- Insert sample user (password: user123)
INSERT INTO users (name, email, password, phone, role) VALUES 
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2UheHEEmuMi', '081234567890', 'user');