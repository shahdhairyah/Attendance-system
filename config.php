<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Function to check user role
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

// Function to redirect if wrong role
function requireRole($role) {
    if (!hasRole($role)) {
        header("Location: index.php");
        exit();
    }
}

// Function to send notification
function sendNotification($student_id, $message, $type = 'warning') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (student_id, message, type, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $student_id, $message, $type);
    $stmt->execute();
    $stmt->close();
}

// Function to check attendance and send alerts
function checkAttendanceAlerts($student_id, $subject_id) {
    global $conn;
    
    // Calculate attendance percentage
    $query = "SELECT 
        COUNT(*) as total_classes,
        COUNT(CASE WHEN status = 'P' THEN 1 END) as present_classes
        FROM attendance 
        WHERE student_id = ? AND subject_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $student_id, $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    if ($data['total_classes'] > 0) {
        $percentage = ($data['present_classes'] / $data['total_classes']) * 100;
        
        if ($percentage < 75) {
            // Get subject name
            $subject_query = "SELECT subject_name FROM subjects WHERE id = ?";
            $stmt = $conn->prepare($subject_query);
            $stmt->bind_param("i", $subject_id);
            $stmt->execute();
            $subject_result = $stmt->get_result();
            $subject = $subject_result->fetch_assoc();
            $stmt->close();
            
            $message = "Your attendance in {$subject['subject_name']} is " . round($percentage, 2) . "%. Please attend classes regularly to maintain minimum 75% attendance.";
            sendNotification($student_id, $message, 'warning');
        }
    }
}
?>