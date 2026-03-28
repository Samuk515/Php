-- Harvard University - Student Information System Database Setup
-- Run this SQL in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS harvard_sis;
USE harvard_sis;

-- Users table for login authentication (admin, teacher, student)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('admin', 'teacher', 'student') DEFAULT 'student',
    student_id VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(10),
    course VARCHAR(100),
    enrollment_date DATE,
    status ENUM('Active', 'Inactive', 'Graduated', 'Suspended') DEFAULT 'Active',
    photo VARCHAR(255) DEFAULT 'default.png',
    fees_total DECIMAL(10, 2) DEFAULT 0.00,
    fees_paid DECIMAL(10, 2) DEFAULT 0.00,
    fees_pending DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Notices table for notice board
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    target_audience ENUM('all', 'students', 'teachers') DEFAULT 'all',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: 1234)
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$w5O6nPBSfrUGDR7nGZIpH.YBnYkJUoe6x3IfszTzk1S8g/TWJIqjq', 'System Administrator', 'admin@harvard.edu', 'admin');

-- Insert sample teacher (password: 1234)
INSERT INTO users (username, password, full_name, email, role) VALUES
('teacher1', '$2y$10$z0bGQq9o99imuUTEZq7rSOTlSFbb8LaE6WYp5rW.6se94Zjeej9..', 'John Smith', 'john.smith@harvard.edu', 'teacher');

-- Insert sample students
INSERT INTO students (student_id, first_name, last_name, email, phone, date_of_birth, gender, address, city, state, zip_code, course, enrollment_date, status, fees_total, fees_paid, fees_pending) VALUES
('STU001', 'Alice', 'Johnson', 'alice.johnson@harvard.edu', '9841234567', '2000-05-15', 'Female', '123 Main Street', 'Boston', 'MA', '02138', 'Computer Science', '2024-01-15', 'Active', 50000.00, 30000.00, 20000.00),
('STU002', 'Bob', 'Williams', 'bob.williams@harvard.edu', '9841234568', '2001-08-22', 'Male', '456 Oak Avenue', 'Cambridge', 'MA', '02139', 'Data Science', '2024-01-20', 'Active', 50000.00, 50000.00, 0.00),
('STU003', 'Carol', 'Davis', 'carol.davis@harvard.edu', '9841234569', '1999-12-10', 'Female', '789 Pine Road', 'Boston', 'MA', '02140', 'Software Engineering', '2024-02-01', 'Active', 50000.00, 25000.00, 25000.00),
('STU004', 'David', 'Brown', 'david.brown@harvard.edu', '9841234570', '2000-03-25', 'Male', '321 Elm Street', 'Cambridge', 'MA', '02141', 'Information Technology', '2024-02-10', 'Active', 50000.00, 40000.00, 10000.00),
('STU005', 'Eva', 'Martinez', 'eva.martinez@harvard.edu', '9841234571', '2001-07-18', 'Female', '654 Maple Lane', 'Boston', 'MA', '02142', 'Computer Science', '2024-02-15', 'Active', 50000.00, 50000.00, 0.00);

-- Insert sample student users (password: 1234)
INSERT INTO users (username, password, full_name, email, role, student_id) VALUES
('alice', '$2y$10$zKyqz11QbUq9wRGc8nsJluCTn5OcHEjK5w8N9UAqBMWVy1AbzdL7q', 'Alice Johnson', 'alice.johnson@harvard.edu', 'student', 'STU001'),
('bob', '$2y$10$zKyqz11QbUq9wRGc8nsJluCTn5OcHEjK5w8N9UAqBMWVy1AbzdL7q', 'Bob Williams', 'bob.williams@harvard.edu', 'student', 'STU002'),
('carol', '$2y$10$zKyqz11QbUq9wRGc8nsJluCTn5OcHEjK5w8N9UAqBMWVy1AbzdL7q', 'Carol Davis', 'carol.davis@harvard.edu', 'student', 'STU003'),
('david', '$2y$10$zKyqz11QbUq9wRGc8nsJluCTn5OcHEjK5w8N9UAqBMWVy1AbzdL7q', 'David Brown', 'david.brown@harvard.edu', 'student', 'STU004'),
('eva', '$2y$10$zKyqz11QbUq9wRGc8nsJluCTn5OcHEjK5w8N9UAqBMWVy1AbzdL7q', 'Eva Martinez', 'eva.martinez@harvard.edu', 'student', 'STU005');

-- Insert sample notices
INSERT INTO notices (title, content, target_audience, created_by) VALUES
('BCA 3rd Semester Exams Starting', 'The BCA 3rd semester examinations will commence from Baisakh 14. All students are advised to collect their admit cards from the administration office.', 'students', 1),
('BSc CSIT First Term Result Published', 'The first term results for BSc CSIT have been published. Students can check their results on the university portal.', 'students', 1),
('BHM Practical Exam', 'The practical examinations for BHM program will be held from Baisakh 10. Students must bring their lab manuals.', 'students', 1),
('BCA 4th Semester Practical Exam', 'Practical examinations for BCA 4th semester will begin from Baisakh 25. Contact your department for the schedule.', 'students', 1),
('Faculty Meeting', 'A faculty meeting is scheduled for Chaitra 30 at 2:00 PM in the conference room. All teachers are required to attend.', 'teachers', 1);
