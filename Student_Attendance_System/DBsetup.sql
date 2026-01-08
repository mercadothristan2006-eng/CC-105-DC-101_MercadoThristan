-- Student Attendance System Database Setup

DROP DATABASE IF EXISTS attendance_system;
CREATE DATABASE IF NOT EXISTS attendance_system;
USE attendance_system;

-- Drop existing tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS attendance_records;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS sections;

-- Create sections table
CREATE TABLE sections (
    section_id INT PRIMARY KEY AUTO_INCREMENT,
    section_name VARCHAR(50) UNIQUE NOT NULL,
    grade_level VARCHAR(20),
    room_number VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create students table
CREATE TABLE students (
    student_id VARCHAR(13) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    section_id INT NOT NULL,
    enrollment_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('active', 'dropped') DEFAULT 'active',
    FOREIGN KEY (section_id) REFERENCES sections(section_id) ON DELETE RESTRICT
);

-- Create attendance_records table
CREATE TABLE attendance_records (
    attendance_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(13) NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    notes TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (student_id, attendance_date),
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- Insert sample sections (BSCS format)
INSERT INTO sections (section_name, grade_level, room_number) VALUES
('BSCS 2A', '2nd Year', 'CS-101'),
('BSCS 2B', '2nd Year', 'CS-102'),
('BSCS 2C', '2nd Year', 'CS-103'),
('BSCS 3A', '3rd Year', 'CS-201'),
('BSCS 3B', '3rd Year', 'CS-202');

-- Insert sample students
INSERT INTO students (student_id, first_name, last_name, section_id) VALUES
('12345-67-8901', 'Juan', 'Dela Cruz', 1),
('12345-67-8902', 'Maria', 'Santos', 1),
('12345-67-8903', 'Pedro', 'Reyes', 2),
('12345-67-8904', 'Ana', 'Garcia', 2),
('12345-67-8905', 'Jose', 'Martinez', 3),
('12345-67-8906', 'Sofia', 'Lopez', 3),
('12345-67-8907', 'Miguel', 'Fernandez', 4),
('12345-67-8908', 'Isabel', 'Torres', 4),
('12345-67-8909', 'Carlos', 'Ramos', 5),
('12345-67-8910', 'Elena', 'Morales', 5),
('12345-67-8911', 'Rico', 'Valdez', 1),
('12345-67-8912', 'Liza', 'Navarro', 2);

-- Insert sample attendance records for the past week
INSERT INTO attendance_records (student_id, attendance_date, status, notes) VALUES
-- Monday (4 days ago)
('12345-67-8901', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),
('12345-67-8902', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),
('12345-67-8903', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'absent', 'Sick leave'),
('12345-67-8904', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'late', 'Traffic'),
('12345-67-8905', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),
('12345-67-8906', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),
('12345-67-8907', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),
('12345-67-8908', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'late', 'Transportation'),

-- Tuesday (3 days ago)
('12345-67-8901', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),
('12345-67-8902', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'late', NULL),
('12345-67-8903', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'absent', 'Still sick'),
('12345-67-8904', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),
('12345-67-8905', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),
('12345-67-8906', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),
('12345-67-8907', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'absent', 'Family emergency'),
('12345-67-8908', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),

-- Wednesday (2 days ago)
('12345-67-8901', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8902', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8903', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8904', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8905', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'late', 'Transportation issue'),
('12345-67-8906', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8907', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8908', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),

-- Yesterday
('12345-67-8901', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present', NULL),
('12345-67-8902', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present', NULL),
('12345-67-8903', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present', NULL),
('12345-67-8904', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'late', 'Heavy traffic'),
('12345-67-8905', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present', NULL),
('12345-67-8906', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present', NULL),
('12345-67-8907', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present', NULL),
('12345-67-8908', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present', NULL);

-- Verify data
SELECT 'Sections created:' AS Info, COUNT(*) AS Count FROM sections
UNION ALL
SELECT 'Students created:', COUNT(*) FROM students
UNION ALL
SELECT 'Attendance records:', COUNT(*) FROM attendance_records;