<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST');
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
    case 'deleteStudent':
        deleteStudent($pdo, $input);
        break;
    case 'deleteAttendanceRecord':
        deleteAttendanceRecord($pdo, $input);
        break;
    case 'deleteSection':
        deleteSection($pdo, $input);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

// Delete a student (will cascade delete attendance records)
function deleteStudent($pdo, $data) {
    // Validation
    if (empty($data['student_id'])) {
        echo json_encode(['success' => false, 'error' => 'Student ID is required']);
        return;
    }
    
    try {
        // Check if student exists
        $stmt = $pdo->prepare("SELECT student_id, first_name, last_name FROM students WHERE student_id = ?");
        $stmt->execute([$data['student_id']]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            echo json_encode(['success' => false, 'error' => 'Student not found']);
            return;
        }
        
        // Check how many attendance records will be deleted
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM attendance_records WHERE student_id = ?");
        $stmt->execute([$data['student_id']]);
        $attendance_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Delete student (attendance records will be cascade deleted)
        $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->execute([$data['student_id']]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Student deleted successfully',
            'deleted_student' => $student['first_name'] . ' ' . $student['last_name'],
            'attendance_records_deleted' => $attendance_count
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Delete a specific attendance record
function deleteAttendanceRecord($pdo, $data) {
    // Validation
    if (empty($data['attendance_id'])) {
        echo json_encode(['success' => false, 'error' => 'Attendance ID is required']);
        return;
    }
    
    try {
        // Check if attendance record exists
        $stmt = $pdo->prepare("
            SELECT a.*, s.first_name, s.last_name 
            FROM attendance_records a
            JOIN students s ON a.student_id = s.student_id
            WHERE a.attendance_id = ?
        ");
        $stmt->execute([$data['attendance_id']]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$record) {
            echo json_encode(['success' => false, 'error' => 'Attendance record not found']);
            return;
        }
        
        // Delete attendance record
        $stmt = $pdo->prepare("DELETE FROM attendance_records WHERE attendance_id = ?");
        $stmt->execute([$data['attendance_id']]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Attendance record deleted successfully',
            'deleted_record' => [
                'student' => $record['first_name'] . ' ' . $record['last_name'],
                'date' => $record['attendance_date'],
                'status' => $record['status']
            ]
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Delete a section (only if no students are assigned)
function deleteSection($pdo, $data) {
    // Validation
    if (empty($data['section_id'])) {
        echo json_encode(['success' => false, 'error' => 'Section ID is required']);
        return;
    }
    
    try {
        // Check if section exists
        $stmt = $pdo->prepare("SELECT * FROM sections WHERE section_id = ?");
        $stmt->execute([$data['section_id']]);
        $section = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$section) {
            echo json_encode(['success' => false, 'error' => 'Section not found']);
            return;
        }
        
        // Check if section has students
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM students WHERE section_id = ?");
        $stmt->execute([$data['section_id']]);
        $student_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($student_count > 0) {
            echo json_encode([
                'success' => false, 
                'error' => 'Cannot delete section with assigned students',
                'student_count' => $student_count
            ]);
            return;
        }
        
        // Delete section
        $stmt = $pdo->prepare("DELETE FROM sections WHERE section_id = ?");
        $stmt->execute([$data['section_id']]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Section deleted successfully',
            'deleted_section' => $section['section_name']
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
