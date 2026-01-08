<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'attendance_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch($action) {
    case 'createStudent':
        createStudent($pdo, $input);
        break;
    case 'createSection':
        createSection($pdo, $input);
        break;
    case 'markAttendance':
        markAttendance($pdo, $input);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

// Create a new student
function createStudent($pdo, $data) {
    // Validation
    $required_fields = ['student_id', 'first_name', 'last_name', 'section_id'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'error' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }
    
    // Validate student ID format (00000-00-0000)
    if (!preg_match('/^\d{5}-\d{2}-\d{4}$/', $data['student_id'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid student ID format. Use: 00000-00-0000']);
        return;
    }
    
    // Validate names (letters, spaces, hyphens only)
    if (!preg_match('/^[a-zA-Z\s\-]+$/', $data['first_name']) || 
        !preg_match('/^[a-zA-Z\s\-]+$/', $data['last_name'])) {
        echo json_encode(['success' => false, 'error' => 'Names can only contain letters, spaces, and hyphens']);
        return;
    }
    
    try {
        // Check if student ID already exists
        $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $stmt->execute([$data['student_id']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Student ID already exists']);
            return;
        }
        
        // Check if section exists
        $stmt = $pdo->prepare("SELECT section_id FROM sections WHERE section_id = ?");
        $stmt->execute([$data['section_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Invalid section ID']);
            return;
        }
        
        // Insert student
        $stmt = $pdo->prepare("
            INSERT INTO students (student_id, first_name, last_name, section_id, enrollment_date)
            VALUES (?, ?, ?, ?, CURDATE())
        ");
        $stmt->execute([
            $data['student_id'],
            trim($data['first_name']),
            trim($data['last_name']),
            $data['section_id']
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Student created successfully',
            'student_id' => $data['student_id']
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Create a new section
function createSection($pdo, $data) {
    // Validation
    if (empty($data['section_name'])) {
        echo json_encode(['success' => false, 'error' => 'Section name is required']);
        return;
    }
    
    try {
        // Check if section name already exists
        $stmt = $pdo->prepare("SELECT section_id FROM sections WHERE section_name = ?");
        $stmt->execute([$data['section_name']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Section name already exists']);
            return;
        }
        
        // Insert section
        $stmt = $pdo->prepare("
            INSERT INTO sections (section_name, grade_level, room_number)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            trim($data['section_name']),
            $data['grade_level'] ?? null,
            $data['room_number'] ?? null
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Section created successfully',
            'section_id' => $pdo->lastInsertId()
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Mark attendance for a student
function markAttendance($pdo, $data) {
    // Validation
    $required_fields = ['student_id', 'attendance_date', 'status'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'error' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            return;
        }
    }
    
    // Validate status
    $valid_statuses = ['present', 'absent', 'late'];
    if (!in_array($data['status'], $valid_statuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status. Use: present, absent, or late']);
        return;
    }
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['attendance_date'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid date format. Use: YYYY-MM-DD']);
        return;
    }
    
    try {
        // Check if student exists
        $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ? AND status = 'active'");
        $stmt->execute([$data['student_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Student not found or inactive']);
            return;
        }
        
        // Insert or update attendance (using ON DUPLICATE KEY UPDATE)
        $stmt = $pdo->prepare("
            INSERT INTO attendance_records (student_id, attendance_date, status, notes)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                status = VALUES(status),
                notes = VALUES(notes),
                recorded_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            $data['student_id'],
            $data['attendance_date'],
            $data['status'],
            $data['notes'] ?? null
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Attendance marked successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>