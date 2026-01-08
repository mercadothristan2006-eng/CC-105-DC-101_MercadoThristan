<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');
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
    case 'updateStudent':
        updateStudent($pdo, $input);
        break;
    case 'updateStudentStatus':
        updateStudentStatus($pdo, $input);
        break;
    case 'updateAttendanceStatus':
        updateAttendanceStatus($pdo, $input);
        break;
    case 'updateSection':
        updateSection($pdo, $input);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

// Update student information
function updateStudent($pdo, $data) {
    // Validation
    if (empty($data['student_id'])) {
        echo json_encode(['success' => false, 'error' => 'Student ID is required']);
        return;
    }
    
    try {
        // Check if student exists
        $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $stmt->execute([$data['student_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Student not found']);
            return;
        }
        
        // Build dynamic UPDATE query based on provided fields
        $updateFields = [];
        $params = [];
        
        if (!empty($data['first_name'])) {
            if (!preg_match('/^[a-zA-Z\s\-]+$/', $data['first_name'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid first name format']);
                return;
            }
            $updateFields[] = "first_name = ?";
            $params[] = trim($data['first_name']);
        }
        
        if (!empty($data['last_name'])) {
            if (!preg_match('/^[a-zA-Z\s\-]+$/', $data['last_name'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid last name format']);
                return;
            }
            $updateFields[] = "last_name = ?";
            $params[] = trim($data['last_name']);
        }
        
        if (!empty($data['section_id'])) {
            // Verify section exists
            $stmt = $pdo->prepare("SELECT section_id FROM sections WHERE section_id = ?");
            $stmt->execute([$data['section_id']]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Invalid section ID']);
                return;
            }
            $updateFields[] = "section_id = ?";
            $params[] = $data['section_id'];
        }
        
        if (empty($updateFields)) {
            echo json_encode(['success' => false, 'error' => 'No fields to update']);
            return;
        }
        
        // Add student_id to params for WHERE clause
        $params[] = $data['student_id'];
        
        // Execute update
        $sql = "UPDATE students SET " . implode(", ", $updateFields) . " WHERE student_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Student updated successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Update student status (active/dropped)
function updateStudentStatus($pdo, $data) {
    // Validation
    if (empty($data['student_id']) || empty($data['status'])) {
        echo json_encode(['success' => false, 'error' => 'Student ID and status are required']);
        return;
    }
    
    $valid_statuses = ['active', 'dropped'];
    if (!in_array($data['status'], $valid_statuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status. Use: active or dropped']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE students SET status = ? WHERE student_id = ?");
        $stmt->execute([$data['status'], $data['student_id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Student status updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Student not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Update attendance status
function updateAttendanceStatus($pdo, $data) {
    // Validation
    if (empty($data['attendance_id']) || empty($data['status'])) {
        echo json_encode(['success' => false, 'error' => 'Attendance ID and status are required']);
        return;
    }
    
    $valid_statuses = ['present', 'absent', 'late'];
    if (!in_array($data['status'], $valid_statuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status. Use: present, absent, or late']);
        return;
    }
    
    try {
        $updateFields = ["status = ?"];
        $params = [$data['status']];
        
        // Optionally update notes
        if (isset($data['notes'])) {
            $updateFields[] = "notes = ?";
            $params[] = $data['notes'];
        }
        
        // Add attendance_id to params
        $params[] = $data['attendance_id'];
        
        $sql = "UPDATE attendance_records SET " . implode(", ", $updateFields) . 
               ", recorded_at = CURRENT_TIMESTAMP WHERE attendance_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Attendance updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Attendance record not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Update section information
function updateSection($pdo, $data) {
    // Validation
    if (empty($data['section_id'])) {
        echo json_encode(['success' => false, 'error' => 'Section ID is required']);
        return;
    }
    
    try {
        // Check if section exists
        $stmt = $pdo->prepare("SELECT section_id FROM sections WHERE section_id = ?");
        $stmt->execute([$data['section_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Section not found']);
            return;
        }
        
        // Build dynamic UPDATE query
        $updateFields = [];
        $params = [];
        
        if (!empty($data['section_name'])) {
            // Check if new name already exists (excluding current section)
            $stmt = $pdo->prepare("SELECT section_id FROM sections WHERE section_name = ? AND section_id != ?");
            $stmt->execute([$data['section_name'], $data['section_id']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Section name already exists']);
                return;
            }
            $updateFields[] = "section_name = ?";
            $params[] = trim($data['section_name']);
        }
        
        if (isset($data['grade_level'])) {
            $updateFields[] = "grade_level = ?";
            $params[] = $data['grade_level'];
        }
        
        if (isset($data['room_number'])) {
            $updateFields[] = "room_number = ?";
            $params[] = $data['room_number'];
        }
        
        if (empty($updateFields)) {
            echo json_encode(['success' => false, 'error' => 'No fields to update']);
            return;
        }
        
        // Add section_id to params
        $params[] = $data['section_id'];
        
        // Execute update
        $sql = "UPDATE sections SET " . implode(", ", $updateFields) . " WHERE section_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Section updated successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>