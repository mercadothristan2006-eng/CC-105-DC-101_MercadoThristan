<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database configuration
$host = 'localhost';
$dbname = 'attendance_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get the action from query parameter
$action = $_GET['action'] ?? '';

switch($action) {
    case 'getAllStudents':
        getAllStudents($pdo);
        break;
    case 'getStudentById':
        getStudentById($pdo);
        break;
    case 'getSections':
        getSections($pdo);
        break;
    case 'getAttendanceByDate':
        getAttendanceByDate($pdo);
        break;
    case 'getStudentAttendanceHistory':
        getStudentAttendanceHistory($pdo);
        break;
    case 'getTodayAttendanceSummary':
        getTodayAttendanceSummary($pdo);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

// Fetch all students with section information
function getAllStudents($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT s.*, sec.section_name, sec.grade_level, sec.room_number
            FROM students s
            JOIN sections sec ON s.section_id = sec.section_id
            WHERE s.status = 'active'
            ORDER BY sec.section_name, s.last_name, s.first_name
        ");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $students]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Fetch single student by ID
function getStudentById($pdo) {
    $student_id = $_GET['student_id'] ?? '';
    
    if (empty($student_id)) {
        echo json_encode(['success' => false, 'error' => 'Student ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, sec.section_name, sec.grade_level, sec.room_number
            FROM students s
            JOIN sections sec ON s.section_id = sec.section_id
            WHERE s.student_id = ?
        ");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($student) {
            echo json_encode(['success' => true, 'data' => $student]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Student not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Fetch all sections
function getSections($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT * FROM sections ORDER BY section_name
        ");
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $sections]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Get attendance records by specific date
function getAttendanceByDate($pdo) {
    $date = $_GET['date'] ?? date('Y-m-d');
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                s.student_id,
                s.first_name,
                s.last_name,
                sec.section_name,
                COALESCE(a.status, 'not_marked') as status,
                a.notes,
                a.recorded_at
            FROM students s
            JOIN sections sec ON s.section_id = sec.section_id
            LEFT JOIN attendance_records a ON s.student_id = a.student_id 
                AND a.attendance_date = ?
            WHERE s.status = 'active'
            ORDER BY sec.section_name, s.last_name, s.first_name
        ");
        $stmt->execute([$date]);
        $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $attendance, 'date' => $date]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Get attendance history for a specific student
function getStudentAttendanceHistory($pdo) {
    $student_id = $_GET['student_id'] ?? '';
    $limit = $_GET['limit'] ?? 30;
    
    if (empty($student_id)) {
        echo json_encode(['success' => false, 'error' => 'Student ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                a.*,
                s.first_name,
                s.last_name
            FROM attendance_records a
            JOIN students s ON a.student_id = s.student_id
            WHERE a.student_id = ?
            ORDER BY a.attendance_date DESC
            LIMIT ?
        ");
        $stmt->execute([$student_id, (int)$limit]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $history]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Get today's attendance summary
function getTodayAttendanceSummary($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                sec.section_name,
                COUNT(DISTINCT s.student_id) as total_students,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN a.status IS NULL THEN 1 ELSE 0 END) as not_marked_count
            FROM students s
            JOIN sections sec ON s.section_id = sec.section_id
            LEFT JOIN attendance_records a ON s.student_id = a.student_id 
                AND a.attendance_date = CURDATE()
            WHERE s.status = 'active'
            GROUP BY sec.section_id, sec.section_name
            ORDER BY sec.section_name
        ");
        $stmt->execute();
        $summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $summary, 'date' => date('Y-m-d')]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>