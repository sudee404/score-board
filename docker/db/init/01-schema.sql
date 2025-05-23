CREATE DATABASE IF NOT EXISTS judge_db;
USE judge_db;

CREATE TABLE IF NOT EXISTS judges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judge_id INT NOT NULL,
    user_id INT NOT NULL,
    points INT NOT NULL CHECK (points BETWEEN 0 AND 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample users for testing
INSERT INTO users (name) VALUES 
('John Doe'),
('Jane Smith'),
('Michael Johnson'),
('Emily Brown'),
('David Wilson');
