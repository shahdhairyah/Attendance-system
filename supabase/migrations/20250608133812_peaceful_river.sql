-- Create database
CREATE DATABASE IF NOT EXISTS attendance_system;
USE attendance_system;

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Faculty table
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    designation VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    faculty_id INT,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    credits INT DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('P', 'A') NOT NULL,
    marked_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (student_id, subject_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES faculty(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Timetable table
CREATE TABLE IF NOT EXISTS timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room_no VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Insert sample admin
INSERT INTO admin (name, email, password) VALUES 
('System Admin', 'admin@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample faculty
INSERT INTO faculty (name, email, password, department, phone, designation) VALUES 
('Dr. Sarah Johnson', 'sarah.johnson@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', '9876543210', 'Professor'),
('Prof. Michael Chen', 'michael.chen@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', '9876543211', 'Associate Professor'),
('Dr. Emily Davis', 'emily.davis@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Electronics', '9876543212', 'Assistant Professor'),
('Prof. Robert Wilson', 'robert.wilson@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mechanical', '9876543213', 'Professor');

-- Insert sample subjects
INSERT INTO subjects (subject_name, subject_code, faculty_id, department, semester, credits) VALUES 
('Data Structures', 'CS301', 1, 'Computer Science', 3, 4),
('Database Management', 'CS302', 1, 'Computer Science', 3, 3),
('Web Development', 'CS303', 2, 'Computer Science', 3, 3),
('Computer Networks', 'CS304', 2, 'Computer Science', 4, 4),
('Digital Electronics', 'EC201', 3, 'Electronics', 2, 3),
('Microprocessors', 'EC301', 3, 'Electronics', 3, 4),
('Thermodynamics', 'ME201', 4, 'Mechanical', 2, 3),
('Machine Design', 'ME301', 4, 'Mechanical', 3, 4);

-- Insert sample students
INSERT INTO students (name, roll_no, email, password, department, semester, phone, address) VALUES 
('John Smith', 'CS2021001', 'john.smith@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', 3, '8765432109', '123 Main St, City'),
('Alice Johnson', 'CS2021002', 'alice.johnson@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', 3, '8765432108', '456 Oak Ave, City'),
('Bob Wilson', 'CS2021003', 'bob.wilson@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', 3, '8765432107', '789 Pine St, City'),
('Emma Davis', 'EC2021001', 'emma.davis@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Electronics', 3, '8765432106', '321 Elm St, City'),
('David Brown', 'ME2021001', 'david.brown@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mechanical', 3, '8765432105', '654 Maple Ave, City');

-- Insert sample attendance data
INSERT INTO attendance (student_id, subject_id, date, status, marked_by) VALUES 
-- Data Structures attendance
(1, 1, '2024-01-15', 'P', 1),
(2, 1, '2024-01-15', 'P', 1),
(3, 1, '2024-01-15', 'A', 1),
(1, 1, '2024-01-16', 'P', 1),
(2, 1, '2024-01-16', 'A', 1),
(3, 1, '2024-01-16', 'P', 1),
-- Database Management attendance
(1, 2, '2024-01-15', 'P', 1),
(2, 2, '2024-01-15', 'P', 1),
(3, 2, '2024-01-15', 'P', 1),
-- Web Development attendance
(1, 3, '2024-01-15', 'A', 2),
(2, 3, '2024-01-15', 'P', 2),
(3, 3, '2024-01-15', 'P', 2);

-- Insert sample timetable
INSERT INTO timetable (subject_id, day_of_week, start_time, end_time, room_no) VALUES 
(1, 'Monday', '09:00:00', '10:00:00', 'CS-101'),
(2, 'Monday', '10:00:00', '11:00:00', 'CS-102'),
(3, 'Tuesday', '09:00:00', '10:00:00', 'CS-103'),
(4, 'Tuesday', '10:00:00', '11:00:00', 'CS-104'),
(5, 'Wednesday', '09:00:00', '10:00:00', 'EC-101'),
(6, 'Wednesday', '10:00:00', '11:00:00', 'EC-102'),
(7, 'Thursday', '09:00:00', '10:00:00', 'ME-101'),
(8, 'Thursday', '10:00:00', '11:00:00', 'ME-102');