<?php
require_once '../config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in and is faculty
if (!isLoggedIn() || !hasRole('faculty')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$faculty_id = $_SESSION['user_id'];
$subject_id = (int)$_POST['subject_id'];
$attendance_data = $_POST['attendance'] ?? [];

// Verify that the subject belongs to the logged-in faculty
$verify_query = "SELECT id FROM subjects WHERE id = ? AND faculty_id = ?";
$stmt = $conn->prepare($verify_query);
$stmt->bind_param("ii", $subject_id, $faculty_id);
$stmt->execute();
$verify_result = $stmt->get_result();

if ($verify_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Subject not found or access denied']);
    exit();
}
$stmt->close();

if (empty($attendance_data)) {
    echo json_encode(['success' => false, 'message' => 'No attendance data provided']);
    exit();
}

$current_date = date('Y-m-d');
$success_count = 0;
$error_count = 0;

// Begin transaction
$conn->begin_transaction();

try {
    // Prepare the insert/update statement
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, subject_id, date, status, marked_by, created_at) 
                           VALUES (?, ?, ?, ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE 
                           status = VALUES(status), marked_by = VALUES(marked_by), created_at = NOW()");
    
    foreach ($attendance_data as $student_id => $status) {
        $student_id = (int)$student_id;
        
        // Validate status
        if (!in_array($status, ['P', 'A'])) {
            continue;
        }
        
        // Verify student exists
        $student_check = $conn->prepare("SELECT id FROM students WHERE id = ?");
        $student_check->bind_param("i", $student_id);
        $student_check->execute();
        $student_result = $student_check->get_result();
        
        if ($student_result->num_rows === 0) {
            $error_count++;
            continue;
        }
        $student_check->close();
        
        // Insert/Update attendance record
        $stmt->bind_param("iissi", $student_id, $subject_id, $current_date, $status, $faculty_id);
        
        if ($stmt->execute()) {
            $success_count++;
            
            // Check for low attendance and send notification
            checkAttendanceAlerts($student_id, $subject_id);
        } else {
            $error_count++;
        }
    }
    
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    if ($success_count > 0) {
        echo json_encode([
            'success' => true, 
            'message' => "Attendance saved successfully for {$success_count} student(s)." . 
                        ($error_count > 0 ? " {$error_count} record(s) had errors." : "")
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No attendance records were saved']);
    }
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error saving attendance: ' . $e->getMessage()]);
}
?>