-- Create database
CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

-- Create contacts table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
);

-- Insert sample data (optional)
INSERT INTO contacts (name, email, subject, message) VALUES 
('John Doe', 'john@example.com', 'Web Development Project', 'Hi, I would like to discuss a web development project with you.'),
('Jane Smith', 'jane@example.com', 'Design Consultation', 'I need help with UI/UX design for my startup.');