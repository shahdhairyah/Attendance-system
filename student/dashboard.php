<?php
require_once '../config.php';

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
WHERE a.student_id = ? AND s.department = ? AND s.semester = ?
GROUP BY s.id, s.subject_name, s.subject_code
ORDER BY s.subject_name";

$stmt = $conn->prepare($attendance_query);
$stmt->bind_param("isi", $student_id, $_SESSION['user_department'], $_SESSION['user_semester']);
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

// Get notifications
$notifications_query = "SELECT * FROM notifications WHERE student_id = ? AND is_read = FALSE ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($notifications_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$notifications_result = $stmt->get_result();
$notifications = $notifications_result->fetch_all(MYSQLI_ASSOC);
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
    <title>Student Dashboard - AttendanceHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            /* Light theme colors */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-card: #ffffff;
            --bg-navbar: rgba(255, 255, 255, 0.95);
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-danger: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --gradient-dark: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        [data-theme="dark"] {
            /* Dark theme colors */
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-card: #1e293b;
            --bg-navbar: rgba(15, 23, 42, 0.95);
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            line-height: 1.6;
        }

        /* Theme Toggle Styles */
        .theme-toggle {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
            margin-left: 1rem;
        }

        .theme-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .theme-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            transition: 0.3s;
            border-radius: 30px;
            box-shadow: var(--shadow-md);
        }

        .theme-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .theme-toggle input:checked + .theme-slider {
            background: var(--gradient-dark);
        }

        .theme-toggle input:checked + .theme-slider:before {
            transform: translateX(30px);
            background-color: #fbbf24;
        }

        .theme-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: white;
            transition: opacity 0.3s ease;
        }

        .theme-icon.sun {
            right: 6px;
            opacity: 1;
        }

        .theme-icon.moon {
            left: 6px;
            opacity: 0;
        }

        .theme-toggle input:checked ~ .theme-icon.sun {
            opacity: 0;
        }

        .theme-toggle input:checked ~ .theme-icon.moon {
            opacity: 1;
        }

        /* Navigation */
        .navbar-custom {
            background: var(--bg-navbar) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 0.5rem 1rem !important;
            margin: 0 0.25rem;
        }

        .nav-link:hover {
            background-color: var(--bg-tertiary);
            transform: translateY(-1px);
        }

        .dropdown-menu {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
            border-radius: 12px;
            padding: 0.5rem;
        }

        .dropdown-item {
            color: var(--text-primary);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
        }

        /* Cards */
        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Profile Section */
        .profile-section {
            background: var(--gradient-primary);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .profile-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: rotate 20s linear infinite;
            z-index: 0;
        }

        .profile-content {
            position: relative;
            z-index: 1;
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        /* Statistics Cards */
        .stat-card {
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            border: none;
            transition: all 0.3s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: rotate 15s linear infinite;
            z-index: 0;
        }

        .stat-card-content {
            position: relative;
            z-index: 1;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--shadow-xl);
        }

        .stat-card.overall {
            background: var(--gradient-primary);
        }

        .stat-card.present {
            background: var(--gradient-success);
        }

        .stat-card.total {
            background: var(--gradient-info);
        }

        .stat-card.warning {
            background: var(--gradient-warning);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Subject Cards */
        .subject-card {
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
        }

        .subject-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            transition: all 0.3s ease;
        }

        .subject-card.high-attendance::before {
            background: var(--gradient-success);
        }

        .subject-card.medium-attendance::before {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .subject-card.low-attendance::before {
            background: var(--gradient-warning);
        }

        .subject-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .subject-card:hover::before {
            width: 8px;
        }

        /* Progress Rings */
        .progress-ring {
            width: 80px;
            height: 80px;
        }

        .progress-ring-circle {
            stroke: var(--border-color);
            stroke-width: 6;
            fill: transparent;
            r: 34;
            cx: 40;
            cy: 40;
        }

        .progress-ring-progress {
            stroke-width: 6;
            fill: transparent;
            r: 34;
            cx: 40;
            cy: 40;
            stroke-dasharray: 213.6;
            stroke-dashoffset: 213.6;
            transition: stroke-dashoffset 1s ease;
            stroke-linecap: round;
        }

        .progress-ring-progress.high {
            stroke: url(#gradient-high);
        }

        .progress-ring-progress.medium {
            stroke: url(#gradient-medium);
        }

        .progress-ring-progress.low {
            stroke: url(#gradient-low);
        }

        /* Alert Styles */
        .alert-low-attendance {
            background: var(--gradient-warning);
            border: none;
            color: white;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
        }

        .alert-low-attendance::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Notification Styles */
        .notification-item {
            background: var(--bg-tertiary);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            border-left: 4px solid #ffc107;
        }

        .notification-item:hover {
            transform: translateX(8px);
            box-shadow: var(--shadow-md);
        }

        /* Recent Attendance List */
        .list-group-item {
            background-color: transparent !important;
            border: none !important;
            border-bottom: 1px solid var(--border-color) !important;
            color: var(--text-primary);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background-color: var(--bg-tertiary) !important;
            border-radius: 8px;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .list-group-item:last-child {
            border-bottom: none !important;
        }

        /* Badges */
        .badge {
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
        }

        /* Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline-warning {
            border: 2px solid #ffc107;
            color: #ffc107;
        }

        .btn-outline-warning:hover {
            background: #ffc107;
            color: white;
        }

        /* Loading Animation */
        .loading-animation {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-section {
                padding: 1.5rem;
                text-align: center;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .theme-toggle {
                margin-left: 0.5rem;
                width: 50px;
                height: 25px;
            }

            .theme-slider:before {
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
            }

            .theme-toggle input:checked + .theme-slider:before {
                transform: translateX(25px);
            }

            .progress-ring {
                width: 60px;
                height: 60px;
            }

            .progress-ring-circle,
            .progress-ring-progress {
                r: 26;
                cx: 30;
                cy: 30;
            }
        }

        /* Dark mode specific adjustments */
        [data-theme="dark"] .navbar-toggler-icon {
            filter: invert(1);
        }

        [data-theme="dark"] .progress-ring-circle {
            stroke: #334155;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* Floating action button */
        .fab {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            border: none;
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .fab:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-xl);
        }

        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        [data-theme="dark"] .glass {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                AttendanceHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-line me-2"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item" href="notifications.php">
                                <i class="fas fa-bell me-2"></i>Notifications
                                <?php if (count($notifications) > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo count($notifications); ?></span>
                                <?php endif; ?>
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <label class="theme-toggle">
                            <input type="checkbox" id="themeToggle">
                            <span class="theme-slider"></span>
                            <i class="fas fa-sun theme-icon sun"></i>
                            <i class="fas fa-moon theme-icon moon"></i>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <!-- Profile Section -->
        <div class="profile-section loading-animation">
            <div class="profile-content">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                <i class="fas fa-user-graduate fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                                <p class="mb-0 opacity-75">Ready to track your academic progress?</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-id-card me-2 opacity-75"></i>
                                    <span class="opacity-75">Roll No: </span>
                                    <strong class="ms-1"><?php echo htmlspecialchars($_SESSION['user_roll_no']); ?></strong>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-building me-2 opacity-75"></i>
                                    <span class="opacity-75">Department: </span>
                                    <strong class="ms-1"><?php echo htmlspecialchars($_SESSION['user_department']); ?></strong>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-layer-group me-2 opacity-75"></i>
                                    <span class="opacity-75">Semester: </span>
                                    <strong class="ms-1"><?php echo htmlspecialchars($_SESSION['user_semester']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="glass rounded-3 p-3">
                            <div class="fs-4 fw-bold mb-1" id="currentDate"><?php echo date('l, F j'); ?></div>
                            <div class="fs-5" id="currentTime"><?php echo date('h:i A'); ?></div>
                            <div class="opacity-75 mt-2">
                                <i class="fas fa-calendar-day me-2"></i>
                                <?php echo date('Y'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Attendance Alert -->
        <?php if ($low_attendance_subjects > 0): ?>
            <div class="alert alert-low-attendance loading-animation" role="alert" style="animation-delay: 0.2s;">
                <div class="d-flex align-items-center position-relative">
                    <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading mb-2 fw-bold">⚠️ Attendance Alert!</h5>
                        <p class="mb-0">
                            You have <strong><?php echo $low_attendance_subjects; ?> subject(s)</strong> with attendance below 75%. 
                            Please attend classes regularly to maintain good academic standing.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card overall loading-animation" style="animation-delay: 0.3s;">
                    <div class="stat-card-content">
                        <i class="fas fa-percentage fa-2x mb-3 opacity-75"></i>
                        <div class="stat-number"><?php echo $overall_percentage; ?>%</div>
                        <div class="stat-label">Overall Attendance</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card present loading-animation" style="animation-delay: 0.4s;">
                    <div class="stat-card-content">
                        <i class="fas fa-check-circle fa-2x mb-3 opacity-75"></i>
                        <div class="stat-number"><?php echo $total_present; ?></div>
                        <div class="stat-label">Classes Attended</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card total loading-animation" style="animation-delay: 0.5s;">
                    <div class="stat-card-content">
                        <i class="fas fa-calendar-alt fa-2x mb-3 opacity-75"></i>
                        <div class="stat-number"><?php echo $total_classes; ?></div>
                        <div class="stat-label">Total Classes</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning loading-animation" style="animation-delay: 0.6s;">
                    <div class="stat-card-content">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3 opacity-75"></i>
                        <div class="stat-number"><?php echo $low_attendance_subjects; ?></div>
                        <div class="stat-label">Low Attendance</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Subject-wise Attendance -->
            <div class="col-lg-8 mb-4">
                <div class="card loading-animation" style="animation-delay: 0.7s;">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-chart-bar me-2 text-primary"></i>Subject-wise Attendance
                            </h5>
                            <span class="badge bg-primary"><?php echo count($attendance_data); ?> Subjects</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($attendance_data)): ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-clipboard-list fa-4x text-muted opacity-50"></i>
                                </div>
                                <h5 class="text-muted">No Attendance Records</h5>
                                <p class="text-muted">Your attendance records will appear here once classes begin.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($attendance_data as $index => $subject): ?>
                                    <?php
                                    $percentage = $subject['attendance_percentage'];
                                    $status_class = $percentage >= 75 ? 'high' : ($percentage >= 60 ? 'medium' : 'low');
                                    $card_class = $percentage >= 75 ? 'high-attendance' : ($percentage >= 60 ? 'medium-attendance' : 'low-attendance');
                                    ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="subject-card card" style="animation-delay: <?php echo 0.8 + ($index * 0.1); ?>s;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="flex-grow-1">
                                                        <h6 class="fw-bold mb-1">
                                                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-code me-1"></i>
                                                            <?php echo htmlspecialchars($subject['subject_code']); ?>
                                                        </small>
                                                    </div>
                                                    <div class="text-center">
                                                        <svg class="progress-ring" width="80" height="80">
                                                            <defs>
                                                                <linearGradient id="gradient-high" x1="0%" y1="0%" x2="100%" y2="0%">
                                                                    <stop offset="0%" style="stop-color:#11998e"/>
                                                                    <stop offset="100%" style="stop-color:#38ef7d"/>
                                                                </linearGradient>
                                                                <linearGradient id="gradient-medium" x1="0%" y1="0%" x2="100%" y2="0%">
                                                                    <stop offset="0%" style="stop-color:#ffc107"/>
                                                                    <stop offset="100%" style="stop-color:#fd7e14"/>
                                                                </linearGradient>
                                                                <linearGradient id="gradient-low" x1="0%" y1="0%" x2="100%" y2="0%">
                                                                    <stop offset="0%" style="stop-color:#f093fb"/>
                                                                    <stop offset="100%" style="stop-color:#f5576c"/>
                                                                </linearGradient>
                                                            </defs>
                                                            <circle class="progress-ring-circle" r="34" cx="40" cy="40"></circle>
                                                            <circle class="progress-ring-progress <?php echo $status_class; ?>" 
                                                                    r="34" cx="40" cy="40" 
                                                                    data-percentage="<?php echo $percentage; ?>"></circle>
                                                        </svg>
                                                        <div class="fw-bold mt-2"><?php echo $percentage; ?>%</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <div class="p-2 bg-success bg-opacity-10 rounded-3">
                                                            <small class="text-muted d-block">Present</small>
                                                            <div class="fw-bold text-success"><?php echo $subject['present_classes']; ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="p-2 bg-danger bg-opacity-10 rounded-3">
                                                            <small class="text-muted d-block">Absent</small>
                                                            <div class="fw-bold text-danger"><?php echo $subject['absent_classes']; ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="p-2 bg-primary bg-opacity-10 rounded-3">
                                                            <small class="text-muted d-block">Total</small>
                                                            <div class="fw-bold text-primary"><?php echo $subject['total_classes']; ?></div>
                                                        </div>
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

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Notifications -->
                <div class="card mb-4 loading-animation" style="animation-delay: 0.8s;">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-bell me-2 text-warning"></i>Notifications
                            </h5>
                            <?php if (count($notifications) > 0): ?>
                                <span class="badge bg-danger"><?php echo count($notifications); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($notifications)): ?>
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                                </div>
                                <h6 class="text-muted">All caught up!</h6>
                                <p class="text-muted mb-0">No new notifications at the moment.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-2 fw-medium"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('M j, g:i A', strtotime($notification['created_at'])); ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-<?php echo $notification['type']; ?> ms-2">
                                            <?php echo ucfirst($notification['type']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center mt-3">
                                <a href="notifications.php" class="btn btn-outline-warning">
                                    <i class="fas fa-eye me-2"></i>View All
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Attendance -->
                <div class="card loading-animation" style="animation-delay: 0.9s;">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-history me-2 text-info"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_attendance)): ?>
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-times fa-3x text-muted opacity-50"></i>
                                </div>
                                <h6 class="text-muted">No recent activity</h6>
                                <p class="text-muted mb-0">Your recent attendance will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_attendance as $record): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">
                                                    <?php echo htmlspecialchars($record['subject_name']); ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('M j, Y', strtotime($record['date'])); ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-<?php echo $record['status'] === 'P' ? 'success' : 'danger'; ?> mb-1">
                                                    <?php echo $record['status'] === 'P' ? 'Present' : 'Absent'; ?>
                                                </span>
                                                <div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($record['subject_code']); ?></small>
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
        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fab" onclick="scrollToTop()" title="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme functionality
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        body.setAttribute('data-theme', currentTheme);

        // Set the toggle state based on current theme
        if (currentTheme === 'dark') {
            themeToggle.checked = true;
        }

        // Theme toggle event listener
        themeToggle.addEventListener('change', function() {
            if (this.checked) {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                body.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            }
        });

        // Animate progress rings
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation classes
            const elements = document.querySelectorAll('.loading-animation');
            elements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.1}s`;
            });

            // Progress rings animation
            setTimeout(() => {
                const progressRings = document.querySelectorAll('.progress-ring-progress');
                
                progressRings.forEach(ring => {
                    const percentage = ring.getAttribute('data-percentage');
                    const circumference = 2 * Math.PI * 34; // r = 34
                    const offset = circumference - (percentage / 100) * circumference;
                    
                    ring.style.strokeDasharray = circumference;
                    ring.style.strokeDashoffset = circumference;
                    
                    setTimeout(() => {
                        ring.style.strokeDashoffset = offset;
                    }, 500);
                });
            }, 1000);
        });

        // Update time every second
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric'
            });
            
            const timeElement = document.getElementById('currentTime');
            const dateElement = document.getElementById('currentDate');
            
            if (timeElement) timeElement.textContent = timeString;
            if (dateElement) dateElement.textContent = dateString;
        }

        setInterval(updateTime, 1000);

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Show/hide floating action button based on scroll
        window.addEventListener('scroll', function() {
            const fab = document.querySelector('.fab');
            if (window.scrollY > 300) {
                fab.style.opacity = '1';
                fab.style.transform = 'scale(1)';
            } else {
                fab.style.opacity = '0';
                fab.style.transform = 'scale(0.8)';
            }
        });

        // Initialize FAB state
        document.querySelector('.fab').style.opacity = '0';
        document.querySelector('.fab').style.transform = 'scale(0.8)';

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add hover effects to cards
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Notification click handler
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                this.style.opacity = '0.7';
                setTimeout(() => {
                    this.style.opacity = '1';
                }, 200);
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Preload theme preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                body.setAttribute('data-theme', savedTheme);
                themeToggle.checked = savedTheme === 'dark';
            }
        });
    </script>
</body>
</html>