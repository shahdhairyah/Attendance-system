<?php
require_once 'config.php';

requireLogin();
requireRole('student');

$student_id = $_SESSION['user_id'];

// Get student's attendance data
$attendance_query = "SELECT 
    s.id as subject_id,
    s.subject_name,
    s.subject_code,
    COUNT(a.id) as total_classes,
    COUNT(CASE WHEN a.status = 'P' THEN 1 END) as present_classes,
    COUNT(CASE WHEN a.status = 'A' THEN 1 END) as absent_classes,
    ROUND((COUNT(CASE WHEN a.status = 'P' THEN 1 END) / COUNT(a.id)) * 100, 2) as attendance_percentage
FROM subjects s
JOIN attendance a ON s.id = a.subject_id
WHERE a.student_id = ? AND s.department = ?
GROUP BY s.id, s.subject_name, s.subject_code
ORDER BY s.subject_name";

$stmt = $conn->prepare($attendance_query);
$stmt->bind_param("is", $student_id, $_SESSION['user_department']);
$stmt->execute();
$attendance_result = $stmt->get_result();
$attendance_data = $attendance_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get recent attendance records
$recent_query = "SELECT 
    s.subject_name,
    s.subject_code,
    a.date,
    a.status
FROM attendance a
JOIN subjects s ON a.subject_id = s.id
WHERE a.student_id = ?
ORDER BY a.date DESC, s.subject_name
LIMIT 10";

$stmt = $conn->prepare($recent_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$recent_result = $stmt->get_result();
$recent_attendance = $recent_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate overall statistics
$total_classes = 0;
$total_present = 0;
$low_attendance_subjects = 0;

foreach ($attendance_data as $subject) {
    $total_classes += $subject['total_classes'];
    $total_present += $subject['present_classes'];
    if ($subject['attendance_percentage'] < 75) {
        $low_attendance_subjects++;
    }
}

$overall_percentage = $total_classes > 0 ? round(($total_present / $total_classes) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Digital Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            color: white;
        }
        
        .stat-card.overall {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.present {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .stat-card.total {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        
        .attendance-bar {
            height: 8px;
            border-radius: 10px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .attendance-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        
        .attendance-fill.high {
            background: linear-gradient(90deg, #28a745, #20c997);
        }
        
        .attendance-fill.medium {
            background: linear-gradient(90deg, #ffc107, #fd7e14);
        }
        
        .attendance-fill.low {
            background: linear-gradient(90deg, #dc3545, #c82333);
        }
        
        .profile-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .subject-card {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .subject-card.high-attendance {
            border-left-color: #28a745;
        }
        
        .subject-card.medium-attendance {
            border-left-color: #ffc107;
        }
        
        .subject-card.low-attendance {
            border-left-color: #dc3545;
        }
        
        .recent-attendance .list-group-item {
            border: none;
            border-radius: 10px !important;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .status-badge.present {
            background-color: #28a745;
        }
        
        .status-badge.absent {
            background-color: #dc3545;
        }
        
        .alert-low-attendance {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
            border-radius: 15px;
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
                            <li><a class="dropdown-item" href="student_reports.php">
                                <i class="fas fa-chart-line me-2"></i>Detailed Reports
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
                    <p class="mb-1 opacity-75">
                        <i class="fas fa-id-card me-2"></i>Roll No: <?php echo htmlspecialchars($_SESSION['user_roll_no']); ?>
                    </p>
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

        <!-- Low Attendance Alert -->
        <?php if ($low_attendance_subjects > 0): ?>
            <div class="alert alert-low-attendance" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">⚠️ Attendance Warning!</h5>
                        <p class="mb-0">
                            You have <?php echo $low_attendance_subjects; ?> subject(s) with attendance below 75%. 
                            Please attend classes regularly to maintain good standing.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card overall">
                    <i class="fas fa-percentage fa-2x mb-2"></i>
                    <div class="fs-2 fw-bold"><?php echo $overall_percentage; ?>%</div>
                    <div class="opacity-75">Overall Attendance</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card present">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <div class="fs-2 fw-bold"><?php echo $total_present; ?></div>
                    <div class="opacity-75">Classes Attended</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card total">
                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                    <div class="fs-2 fw-bold"><?php echo $total_classes; ?></div>
                    <div class="opacity-75">Total Classes</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <div class="fs-2 fw-bold"><?php echo $low_attendance_subjects; ?></div>
                    <div class="opacity-75">Low Attendance</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Subject-wise Attendance -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Subject-wise Attendance
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($attendance_data)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                <h5>No Attendance Records</h5>
                                <p>Your attendance records will appear here once classes begin.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($attendance_data as $subject): ?>
                                    <?php
                                    $percentage = $subject['attendance_percentage'];
                                    $status_class = $percentage >= 75 ? 'high' : ($percentage >= 60 ? 'medium' : 'low');
                                    $card_class = $percentage >= 75 ? 'high-attendance' : ($percentage >= 60 ? 'medium-attendance' : 'low-attendance');
                                    ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card subject-card <?php echo $card_class; ?>">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="card-title fw-bold mb-1">
                                                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($subject['subject_code']); ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-<?php echo $status_class === 'high' ? 'success' : ($status_class === 'medium' ? 'warning' : 'danger'); ?> fs-6">
                                                        <?php echo $percentage; ?>%
                                                    </span>
                                                </div>
                                                
                                                <div class="attendance-bar mb-2">
                                                    <div class="attendance-fill <?php echo $status_class; ?>" 
                                                         style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                                
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <small class="text-muted">Present</small>
                                                        <div class="fw-bold text-success"><?php echo $subject['present_classes']; ?></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">Absent</small>
                                                        <div class="fw-bold text-danger"><?php echo $subject['absent_classes']; ?></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">Total</small>
                                                        <div class="fw-bold text-primary"><?php echo $subject['total_classes']; ?></div>
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

            <!-- Recent Attendance -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Recent Attendance
                        </h5>
                    </div>
                    <div class="card-body recent-attendance">
                        <?php if (empty($recent_attendance)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <p class="mb-0">No recent records found.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_attendance as $record): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 fw-bold">
                                                    <?php echo htmlspecialchars($record['subject_name']); ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('M j, Y', strtotime($record['date'])); ?>
                                                </small>
                                            </div>
                                            <span class="badge status-badge <?php echo strtolower($record['status'] === 'P' ? 'present' : 'absent'); ?>">
                                                <?php echo $record['status'] === 'P' ? 'Present' : 'Absent'; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate attendance bars on page load
        document.addEventListener('DOMContentLoaded', function() {
            const attendanceFills = document.querySelectorAll('.attendance-fill');
            attendanceFills.forEach(fill => {
                const width = fill.style.width;
                fill.style.width = '0%';
                setTimeout(() => {
                    fill.style.width = width;
                }, 500);
            });
        });
    </script>
</body>
</html>