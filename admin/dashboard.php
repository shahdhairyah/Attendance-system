<?php
require_once '../config.php';

requireLogin();
requireRole('admin');

// Get statistics
$stats = [];

// Total students
$result = $conn->query("SELECT COUNT(*) as count FROM students");
$stats['students'] = $result->fetch_assoc()['count'];

// Total faculty
$result = $conn->query("SELECT COUNT(*) as count FROM faculty");
$stats['faculty'] = $result->fetch_assoc()['count'];

// Total subjects
$result = $conn->query("SELECT COUNT(*) as count FROM subjects");
$stats['subjects'] = $result->fetch_assoc()['count'];

// Total departments
$result = $conn->query("SELECT COUNT(DISTINCT department) as count FROM students");
$stats['departments'] = $result->fetch_assoc()['count'];

// Recent activities
$recent_query = "SELECT 
    s.name as student_name,
    sub.subject_name,
    a.date,
    a.status,
    f.name as faculty_name
FROM attendance a
JOIN students s ON a.student_id = s.id
JOIN subjects sub ON a.subject_id = sub.id
JOIN faculty f ON a.marked_by = f.id
ORDER BY a.created_at DESC
LIMIT 10";

$recent_result = $conn->query($recent_query);
$recent_activities = $recent_result->fetch_all(MYSQLI_ASSOC);

// Department-wise statistics
$dept_query = "SELECT 
    s.department,
    COUNT(DISTINCT s.id) as student_count,
    COUNT(DISTINCT sub.id) as subject_count,
    COUNT(DISTINCT f.id) as faculty_count
FROM students s
LEFT JOIN subjects sub ON s.department = sub.department
LEFT JOIN faculty f ON s.department = f.department
GROUP BY s.department";

$dept_result = $conn->query($dept_query);
$dept_stats = $dept_result->fetch_all(MYSQLI_ASSOC);

// Low attendance students
$low_attendance_query = "SELECT 
    s.name,
    s.roll_no,
    s.department,
    sub.subject_name,
    COUNT(*) as total_classes,
    COUNT(CASE WHEN a.status = 'P' THEN 1 END) as present_classes,
    ROUND((COUNT(CASE WHEN a.status = 'P' THEN 1 END) / COUNT(*)) * 100, 2) as percentage
FROM attendance a
JOIN students s ON a.student_id = s.id
JOIN subjects sub ON a.subject_id = sub.id
GROUP BY s.id, sub.id
HAVING percentage < 75
ORDER BY percentage ASC
LIMIT 10";

$low_attendance_result = $conn->query($low_attendance_query);
$low_attendance = $low_attendance_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AttendanceHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .top-navbar {
            background: white;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }
        
        .stat-card.students::before {
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .stat-card.faculty::before {
            background: linear-gradient(90deg, #11998e, #38ef7d);
        }
        
        .stat-card.subjects::before {
            background: linear-gradient(90deg, #f093fb, #f5576c);
        }
        
        .stat-card.departments::before {
            background: linear-gradient(90deg, #4facfe, #00f2fe);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand">
                <i class="fas fa-graduation-cap me-2"></i>
                AttendanceHub
            </a>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="students.php">
                        <i class="fas fa-user-graduate me-2"></i>Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="faculty.php">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Faculty
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="subjects.php">
                        <i class="fas fa-book me-2"></i>Subjects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="departments.php">
                        <i class="fas fa-building me-2"></i>Departments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">
                        <i class="fas fa-chart-bar me-2"></i>Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold">Admin Dashboard</h4>
                <small class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="profile.php">
                        <i class="fas fa-user me-2"></i>Profile
                    </a></li>
                    <li><a class="dropdown-item" href="settings.php">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a></li>
                </ul>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card students">
                    <i class="fas fa-user-graduate stat-icon"></i>
                    <div class="stat-number text-primary"><?php echo $stats['students']; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card faculty">
                    <i class="fas fa-chalkboard-teacher stat-icon"></i>
                    <div class="stat-number text-success"><?php echo $stats['faculty']; ?></div>
                    <div class="stat-label">Faculty Members</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card subjects">
                    <i class="fas fa-book stat-icon"></i>
                    <div class="stat-number text-danger"><?php echo $stats['subjects']; ?></div>
                    <div class="stat-label">Total Subjects</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card departments">
                    <i class="fas fa-building stat-icon"></i>
                    <div class="stat-number text-info"><?php echo $stats['departments']; ?></div>
                    <div class="stat-label">Departments</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Department Statistics Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Department-wise Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Attendance Alert -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Attendance Alert
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($low_attendance)): ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                <p class="mb-0">All students have good attendance!</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($low_attendance, 0, 5) as $student): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($student['name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($student['subject_name']); ?></small>
                                            </div>
                                            <span class="badge bg-danger"><?php echo $student['percentage']; ?>%</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Recent Attendance Activities
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_activities)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                <h5>No Recent Activities</h5>
                                <p>Attendance activities will appear here once faculty start marking attendance.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Marked By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_activities as $activity): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($activity['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($activity['subject_name']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($activity['date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $activity['status'] === 'P' ? 'success' : 'danger'; ?>">
                                                        <?php echo $activity['status'] === 'P' ? 'Present' : 'Absent'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($activity['faculty_name']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Department Statistics Chart
        const ctx = document.getElementById('departmentChart').getContext('2d');
        const departmentData = <?php echo json_encode($dept_stats); ?>;
        
        const labels = departmentData.map(dept => dept.department);
        const studentCounts = departmentData.map(dept => dept.student_count);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: studentCounts,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(17, 153, 142, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgba(102, 126, 234, 1)',
                        'rgba(17, 153, 142, 1)',
                        'rgba(240, 147, 251, 1)',
                        'rgba(79, 172, 254, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' students';
                            }
                        }
                    }
                }
            }
        });

        // Animate stat numbers
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animate all stat numbers on page load
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(element => {
                const finalValue = parseInt(element.textContent);
                element.textContent = '0';
                setTimeout(() => {
                    animateValue(element, 0, finalValue, 1500);
                }, 500);
            });
        });
    </script>
</body>
</html>