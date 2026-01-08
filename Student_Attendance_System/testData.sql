-- Insert sample sections
INSERT INTO sections (section_name, grade_level, room_number) VALUES
('Section A', 'Grade 10', '101'),
('Section B', 'Grade 10', '102'),
('Section C', 'Grade 11', '201'),
('Section D', 'Grade 11', '202');

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
('12345-67-8909', 'Carlos', 'Ramos', 1),
('12345-67-8910', 'Elena', 'Morales', 2);

-- Insert sample attendance records for the past week
INSERT INTO attendance_records (student_id, attendance_date, status, notes) VALUES
-- Monday
('12345-67-8901', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),
('12345-67-8902', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),
('12345-67-8903', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'absent', 'Sick leave'),
('12345-67-8904', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'late', 'Traffic'),
('12345-67-8905', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'present', NULL),

-- Tuesday
('12345-67-8901', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),
('12345-67-8902', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'late', NULL),
('12345-67-8903', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'absent', 'Still sick'),
('12345-67-8904', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),
('12345-67-8905', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present', NULL),

-- Wednesday
('12345-67-8901', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8902', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8903', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8904', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'present', NULL),
('12345-67-8905', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'late', 'Transportation issue');
