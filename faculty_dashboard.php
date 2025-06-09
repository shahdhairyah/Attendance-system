<?php
require_once 'config.php';

requireLogin();
requireRole('faculty');

$faculty_id = $_SESSION['user_id'];

// Get faculty's subjects
$subjects_query = "SELECT id, subject_name, subject_code FROM subjects WHERE faculty_id = ?";
$stmt = $conn->prepare($subjects_query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$subjects_result = $stmt->get_result();
$subjects = $subjects_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get students for selected subject
$students = [];
$selected_subject = null;
if (isset($_GET['subject_id']) && !empty($_GET['subject_id'])) {
    $subject_id = (int)$_GET['subject_id'];
    
    // Verify this subject belongs to the logged-in faculty
    $verify_query = "SELECT subject_name, subject_code FROM subjects WHERE id = ? AND faculty_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $subject_id, $faculty_id);
    $stmt->execute();
    $verify_result = $stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $selected_subject = $verify_result->fetch_assoc();
        $selected_subject['id'] = $subject_id;
        
        // Get students from the same department
        $students_query = "SELECT s.id, s.name, s.roll_no, s.department,
                          COALESCE(a.status, '') as today_status
                          FROM students s
                          LEFT JOIN attendance a ON s.id = a.student_id 
                          AND a.subject_id = ? AND a.date = CURDATE()
                          WHERE s.department = (SELECT department FROM subjects WHERE id = ?)
                          ORDER BY s.roll_no";
        $stmt = $conn->prepare($students_query);
        $stmt->bind_param("ii", $subject_id, $subject_id);
        $stmt->execute();
        $students_result = $stmt->get_result();
        $students = $students_result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}

// Get recent attendance statistics
$stats_query = "SELECT 
    COUNT(DISTINCT a.student_id) as total_students,
    COUNT(CASE WHEN a.status = 'P' THEN 1 END) as present_count,
    COUNT(CASE WHEN a.status = 'A' THEN 1 END) as absent_count,
    s.subject_name
FROM attendance a 
JOIN subjects s ON a.subject_id = s.id 
WHERE s.faculty_id = ? AND a.date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY s.id, s.subject_name";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$stats_result = $stmt->get_result();
$statistics = $stats_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - Digital Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        
        .stat-card .card-body {
            padding: 2rem;
        }
        
        .subject-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .subject-card:hover {
            background-color: #f8f9fa;
        }
        
        .subject-card.active {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }
        
        .attendance-table th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
        }
        
        .btn-attendance {
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 0.875rem;
            font-weight: 600;
            min-width: 80px;
        }
        
        .present-btn {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .absent-btn {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .profile-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                Digital Attendance System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="attendance_reports.php">
                                <i class="fas fa-chart-bar me-2"></i>View Reports
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-building me-2"></i>Department: <?php echo htmlspecialchars($_SESSION['user_department']); ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="fs-5 fw-bold"><?php echo date('l, F j, Y'); ?></div>
                    <div class="opacity-75"><?php echo date('h:i A'); ?></div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Subjects List -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-book me-2"></i>Your Subjects
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($subjects)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-info-circle mb-2"></i>
                                <p class="mb-0">No subjects assigned yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($subjects as $subject): ?>
                                    <a href="?subject_id=<?php echo $subject['id']; ?>" 
                                       class="list-group-item list-group-item-action subject-card <?php echo (isset($_GET['subject_id']) && $_GET['subject_id'] == $subject['id']) ? 'active' : ''; ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($subject['subject_name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($subject['subject_code']); ?></small>
                                            </div>
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <?php if ($selected_subject): ?>
                    <!-- Attendance Marking -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clipboard-check me-2"></i>
                                Mark Attendance - <?php echo htmlspecialchars($selected_subject['subject_name']); ?>
                            </h5>
                            <small class="opacity-75">
                                Subject Code: <?php echo htmlspecialchars($selected_subject['subject_code']); ?> | 
                                Date: <?php echo date('Y-m-d'); ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <?php if (empty($students)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h5>No students found</h5>
                                    <p>No students are enrolled in this subject's department.</p>
                                </div>
                            <?php else: ?>
                                <form id="attendanceForm">
                                    <input type="hidden" name="subject_id" value="<?php echo $selected_subject['id']; ?>">
                                    <div class="table-responsive">
                                        <table class="table table-hover attendance-table">
                                            <thead>
                                                <tr>
                                                    <th>Roll No</th>
                                                    <th>Student Name</th>
                                                    <th>Department</th>
                                                    <th>Attendance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($students as $student): ?>
                                                    <tr>
                                                        <td class="fw-bold"><?php echo htmlspecialchars($student['roll_no']); ?></td>
                                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?php echo htmlspecialchars($student['department']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <input type="radio" class="btn-check" 
                                                                       name="attendance[<?php echo $student['id']; ?>]" 
                                                                       id="present_<?php echo $student['id']; ?>" 
                                                                       value="P" 
                                                                       <?php echo ($student['today_status'] == 'P') ? 'checked' : ''; ?>>
                                                                <label class="btn btn-outline-success btn-attendance present-btn" 
                                                                       for="present_<?php echo $student['id']; ?>">
                                                                    <i class="fas fa-check me-1"></i>Present
                                                                </label>
                                                                
                                                                <input type="radio" class="btn-check" 
                                                                       name="attendance[<?php echo $student['id']; ?>]" 
                                                                       id="absent_<?php echo $student['id']; ?>" 
                                                                       value="A" 
                                                                       <?php echo ($student['today_status'] == 'A') ? 'checked' : ''; ?>>
                                                                <label class="btn btn-outline-danger btn-attendance absent-btn" 
                                                                       for="absent_<?php echo $student['id']; ?>">
                                                                    <i class="fas fa-times me-1"></i>Absent
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>Save Attendance
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Welcome Message -->
                    <div class="card text-center">
                        <div class="card-body py-5">
                            <i class="fas fa-clipboard-list fa-5x text-primary mb-4"></i>
                            <h3 class="card-title">Select a Subject</h3>
                            <p class="card-text text-muted">
                                Choose a subject from the left panel to start marking attendance for your students.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <?php if (!empty($statistics)): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="fas fa-chart-bar me-2"></i>Weekly Attendance Overview
                            </h5>
                        </div>
                        <?php foreach ($statistics as $stat): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($stat['subject_name']); ?></h6>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fs-4 fw-bold"><?php echo $stat['total_students']; ?></div>
                                                <small class="opacity-75">Students</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fs-4 fw-bold text-success"><?php echo $stat['present_count']; ?></div>
                                                <small class="opacity-75">Present</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fs-4 fw-bold text-danger"><?php echo $stat['absent_count']; ?></div>
                                                <small class="opacity-75">Absent</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const attendanceForm = document.getElementById('attendanceForm');
            if (attendanceForm) {
                attendanceForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
                    
                    fetch('attendance_submit.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = `
                                <i class="fas fa-check-circle me-2"></i>
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            attendanceForm.insertBefore(alert, attendanceForm.firstChild);
                            
                            // Auto-dismiss after 3 seconds
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        } else {
                            // Show error message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-danger alert-dismissible fade show';
                            alert.innerHTML = `
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            attendanceForm.insertBefore(alert, attendanceForm.firstChild);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-danger alert-dismissible fade show';
                        alert.innerHTML = `
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            An error occurred while saving attendance.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        attendanceForm.insertBefore(alert, attendanceForm.firstChild);
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });
    </script>
</body>
</html>