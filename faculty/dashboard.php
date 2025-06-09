<?php
require_once '../config.php';

requireLogin();
requireRole('faculty');

$faculty_id = $_SESSION['user_id'];

// Get faculty's subjects
$subjects_query = "SELECT id, subject_name, subject_code, semester FROM subjects WHERE faculty_id = ? ORDER BY subject_name";
$stmt = $conn->prepare($subjects_query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$subjects_result = $stmt->get_result();
$subjects = $subjects_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get today's timetable
$today = date('l'); // Get current day name
$timetable_query = "SELECT 
    s.subject_name,
    s.subject_code,
    t.start_time,
    t.end_time,
    t.room_no
FROM timetable t
JOIN subjects s ON t.subject_id = s.id
WHERE s.faculty_id = ? AND t.day_of_week = ?
ORDER BY t.start_time";

$stmt = $conn->prepare($timetable_query);
$stmt->bind_param("is", $faculty_id, $today);
$stmt->execute();
$timetable_result = $stmt->get_result();
$today_schedule = $timetable_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get students for selected subject
$students = [];
$selected_subject = null;
if (isset($_GET['subject_id']) && !empty($_GET['subject_id'])) {
    $subject_id = (int)$_GET['subject_id'];
    
    // Verify this subject belongs to the logged-in faculty
    $verify_query = "SELECT subject_name, subject_code, semester FROM subjects WHERE id = ? AND faculty_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $subject_id, $faculty_id);
    $stmt->execute();
    $verify_result = $stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $selected_subject = $verify_result->fetch_assoc();
        $selected_subject['id'] = $subject_id;
        
        // Get students from the same department and semester
        $students_query = "SELECT s.id, s.name, s.roll_no, s.department, s.semester,
                          COALESCE(a.status, '') as today_status
                          FROM students s
                          LEFT JOIN attendance a ON s.id = a.student_id 
                          AND a.subject_id = ? AND a.date = CURDATE()
                          WHERE s.department = (SELECT department FROM subjects WHERE id = ?)
                          AND s.semester = (SELECT semester FROM subjects WHERE id = ?)
                          ORDER BY s.roll_no";
        $stmt = $conn->prepare($students_query);
        $stmt->bind_param("iii", $subject_id, $subject_id, $subject_id);
        $stmt->execute();
        $students_result = $stmt->get_result();
        $students = $students_result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}

// Get attendance statistics for faculty's subjects
$stats_query = "SELECT 
    s.subject_name,
    COUNT(DISTINCT st.id) as total_students,
    COUNT(CASE WHEN a.status = 'P' THEN 1 END) as present_count,
    COUNT(CASE WHEN a.status = 'A' THEN 1 END) as absent_count,
    COUNT(DISTINCT a.date) as total_classes
FROM subjects s
LEFT JOIN students st ON s.department = st.department AND s.semester = st.semester
LEFT JOIN attendance a ON s.id = a.subject_id AND st.id = a.student_id
WHERE s.faculty_id = ?
GROUP BY s.id, s.subject_name
ORDER BY s.subject_name";

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
    <title>Faculty Dashboard - AttendanceHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
     /* Modern Faculty Dashboard CSS */
:root {
  /* Color System */
  --primary-50: #eff6ff;
  --primary-100: #dbeafe;
  --primary-200: #bfdbfe;
  --primary-300: #93c5fd;
  --primary-400: #60a5fa;
  --primary-500: #3b82f6;
  --primary-600: #2563eb;
  --primary-700: #1d4ed8;
  --primary-800: #1e40af;
  --primary-900: #1e3a8a;

  --success-50: #ecfdf5;
  --success-100: #d1fae5;
  --success-200: #a7f3d0;
  --success-300: #6ee7b7;
  --success-400: #34d399;
  --success-500: #10b981;
  --success-600: #059669;
  --success-700: #047857;
  --success-800: #065f46;
  --success-900: #064e3b;

  --warning-50: #fffbeb;
  --warning-100: #fef3c7;
  --warning-200: #fde68a;
  --warning-300: #fcd34d;
  --warning-400: #fbbf24;
  --warning-500: #f59e0b;
  --warning-600: #d97706;
  --warning-700: #b45309;
  --warning-800: #92400e;
  --warning-900: #78350f;

  --error-50: #fef2f2;
  --error-100: #fee2e2;
  --error-200: #fecaca;
  --error-300: #fca5a5;
  --error-400: #f87171;
  --error-500: #ef4444;
  --error-600: #dc2626;
  --error-700: #b91c1c;
  --error-800: #991b1b;
  --error-900: #7f1d1d;

  --neutral-50: #f8fafc;
  --neutral-100: #f1f5f9;
  --neutral-200: #e2e8f0;
  --neutral-300: #cbd5e1;
  --neutral-400: #94a3b8;
  --neutral-500: #64748b;
  --neutral-600: #475569;
  --neutral-700: #334155;
  --neutral-800: #1e293b;
  --neutral-900: #0f172a;

  /* Gradients */
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --gradient-error: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  --gradient-cosmic: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-ocean: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  --gradient-sunset: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
  --gradient-aurora: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

  /* Spacing */
  --spacing-1: 0.25rem;
  --spacing-2: 0.5rem;
  --spacing-3: 0.75rem;
  --spacing-4: 1rem;
  --spacing-5: 1.25rem;
  --spacing-6: 1.5rem;
  --spacing-8: 2rem;
  --spacing-10: 2.5rem;
  --spacing-12: 3rem;
  --spacing-16: 4rem;
  --spacing-20: 5rem;

  /* Typography */
  --font-family-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  --font-weight-light: 300;
  --font-weight-normal: 400;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
  --font-weight-bold: 700;
  --font-weight-extrabold: 800;

  /* Border Radius */
  --radius-sm: 0.375rem;
  --radius-md: 0.5rem;
  --radius-lg: 0.75rem;
  --radius-xl: 1rem;
  --radius-2xl: 1.5rem;
  --radius-3xl: 2rem;

  /* Shadows */
  --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  --shadow-inner: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);

  /* Transitions */
  --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-normal: 200ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-slower: 500ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Base Styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  scroll-behavior: smooth;
  font-size: 16px;
}

body {
  font-family: var(--font-family-primary);
  background: linear-gradient(135deg, var(--neutral-50) 0%, var(--neutral-100) 100%);
  color: var(--neutral-800);
  line-height: 1.6;
  min-height: 100vh;
  overflow-x: hidden;
}

/* Modern Navbar */
.navbar-custom {
  background: var(--gradient-cosmic);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: var(--shadow-lg);
  position: relative;
  z-index: 1000;
}

.navbar-custom::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
  backdrop-filter: blur(20px);
  z-index: -1;
}

.navbar-brand {
  font-weight: var(--font-weight-bold);
  font-size: 1.5rem;
  color: white !important;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
  transition: var(--transition-normal);
}

.navbar-brand:hover {
  transform: translateY(-2px);
  text-shadow: 0 4px 20px rgba(255, 255, 255, 0.3);
}

.nav-link {
  color: rgba(255, 255, 255, 0.9) !important;
  font-weight: var(--font-weight-medium);
  padding: var(--spacing-2) var(--spacing-4) !important;
  border-radius: var(--radius-lg);
  transition: var(--transition-normal);
  position: relative;
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
}

.nav-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.1);
  border-radius: var(--radius-lg);
  opacity: 0;
  transition: var(--transition-normal);
}

.nav-link:hover::before {
  opacity: 1;
}

.nav-link:hover {
  color: white !important;
  transform: translateY(-1px);
}

/* Profile Section with Glassmorphism */
.profile-section {
  background: var(--gradient-ocean);
  border-radius: var(--radius-2xl);
  padding: var(--spacing-8);
  margin-bottom: var(--spacing-8);
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-xl);
}

.profile-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-2xl);
}

.profile-section * {
  position: relative;
  z-index: 1;
}

.profile-section h2 {
  font-weight: var(--font-weight-bold);
  font-size: 2.25rem;
  color: white;
  margin-bottom: var(--spacing-3);
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.profile-section p {
  color: rgba(255, 255, 255, 0.9);
  font-weight: var(--font-weight-medium);
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
  margin-bottom: var(--spacing-2);
}

/* Modern Cards */
.card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-lg);
  transition: var(--transition-normal);
  overflow: hidden;
  position: relative;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-cosmic);
  opacity: 0;
  transition: var(--transition-normal);
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-2xl);
}

.card:hover::before {
  opacity: 1;
}

.card-header {
  background: var(--gradient-cosmic);
  color: white;
  padding: var(--spacing-5);
  border-bottom: none;
  position: relative;
  overflow: hidden;
}

.card-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.card-header * {
  position: relative;
  z-index: 1;
}

.card-header h5 {
  font-weight: var(--font-weight-semibold);
  margin: 0;
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
}

.card-body {
  padding: var(--spacing-6);
}

/* Subject Cards with Modern Interaction */
.subject-card {
  cursor: pointer;
  transition: var(--transition-normal);
  border-left: 4px solid transparent;
  border-radius: var(--radius-lg);
  position: relative;
  overflow: hidden;
}

.subject-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--gradient-primary);
  opacity: 0;
  transition: var(--transition-normal);
}

.subject-card:hover {
  background: rgba(102, 126, 234, 0.05);
  border-left-color: var(--primary-500);
  transform: translateX(8px);
}

.subject-card:hover::before {
  opacity: 0.05;
}

.subject-card.active {
  background: var(--primary-50);
  border-left-color: var(--primary-500);
  box-shadow: var(--shadow-md);
}

.subject-card h6 {
  font-weight: var(--font-weight-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--spacing-1);
}

.subject-card small {
  color: var(--neutral-500);
  font-weight: var(--font-weight-medium);
}

/* Schedule Items with Modern Design */
.schedule-item {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: var(--radius-xl);
  padding: var(--spacing-4);
  margin-bottom: var(--spacing-3);
  border-left: 4px solid var(--primary-500);
  transition: var(--transition-normal);
  position: relative;
  overflow: hidden;
}

.schedule-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--gradient-primary);
  opacity: 0;
  transition: var(--transition-normal);
}

.schedule-item:hover {
  transform: translateX(12px);
  box-shadow: var(--shadow-lg);
}

.schedule-item:hover::before {
  opacity: 0.05;
}

.schedule-item h6 {
  font-weight: var(--font-weight-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--spacing-1);
  position: relative;
  z-index: 1;
}

.schedule-item small {
  color: var(--neutral-600);
  font-weight: var(--font-weight-medium);
  display: flex;
  align-items: center;
  gap: var(--spacing-1);
  position: relative;
  z-index: 1;
}

/* Modern Attendance Table */
.attendance-table {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: var(--radius-xl);
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

.attendance-table th {
  background: var(--neutral-100);
  color: var(--neutral-700);
  font-weight: var(--font-weight-semibold);
  padding: var(--spacing-4);
  border: none;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 0.875rem;
}

.attendance-table td {
  padding: var(--spacing-4);
  border-bottom: 1px solid var(--neutral-200);
  vertical-align: middle;
}

.attendance-table tr {
  transition: var(--transition-fast);
}

.attendance-table tr:hover {
  background: rgba(102, 126, 234, 0.05);
}

/* Modern Button System */
.btn-attendance {
  border-radius: var(--radius-xl);
  padding: var(--spacing-2) var(--spacing-4);
  font-size: 0.875rem;
  font-weight: var(--font-weight-semibold);
  min-width: 100px;
  transition: var(--transition-normal);
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--spacing-1);
}

.btn-attendance::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: var(--transition-slow);
}

.btn-attendance:hover::before {
  left: 100%;
}

.btn-present {
  background: var(--gradient-success);
  color: white;
  border-color: var(--success-500);
}

.btn-present:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
  color: white;
}

.btn-absent {
  background: var(--gradient-error);
  color: white;
  border-color: var(--error-500);
}

.btn-absent:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
  color: white;
}

/* Present All Button */
.present-all-btn {
  background: var(--gradient-warning);
  border: none;
  border-radius: var(--radius-xl);
  padding: var(--spacing-3) var(--spacing-6);
  color: white;
  font-weight: var(--font-weight-semibold);
  transition: var(--transition-normal);
  display: inline-flex;
  align-items: center;
  gap: var(--spacing-2);
  position: relative;
  overflow: hidden;
}

.present-all-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: var(--transition-slow);
}

.present-all-btn:hover {
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(245, 158, 11, 0.4);
}

.present-all-btn:hover::before {
  left: 100%;
}

/* Statistics Cards */
.stat-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(15px);
  border-radius: var(--radius-2xl);
  padding: var(--spacing-6);
  text-align: center;
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: var(--transition-normal);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-cosmic);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
}

.stat-card h6 {
  font-weight: var(--font-weight-semibold);
  color: var(--neutral-800);
  margin-bottom: var(--spacing-4);
}

.stat-card .fs-4 {
  font-size: 2rem;
  font-weight: var(--font-weight-bold);
  margin-bottom: var(--spacing-1);
}

/* Quick Actions */
.quick-actions {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(15px);
  border-radius: var(--radius-2xl);
  padding: var(--spacing-6);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.action-btn {
  background: var(--gradient-cosmic);
  border: none;
  border-radius: var(--radius-xl);
  padding: var(--spacing-4);
  color: white;
  text-decoration: none;
  display: block;
  margin-bottom: var(--spacing-4);
  transition: var(--transition-normal);
  position: relative;
  overflow: hidden;
}

.action-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: var(--transition-slow);
}

.action-btn:hover {
  color: white;
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3);
  text-decoration: none;
}

.action-btn:hover::before {
  left: 100%;
}

.action-btn .fw-bold {
  font-weight: var(--font-weight-semibold);
  margin-bottom: var(--spacing-1);
}

/* Badge Styles */
.badge {
  padding: var(--spacing-1) var(--spacing-3);
  border-radius: var(--radius-lg);
  font-size: 0.75rem;
  font-weight: var(--font-weight-semibold);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.bg-info {
  background: var(--gradient-primary) !important;
  color: white;
}

.bg-secondary {
  background: var(--neutral-500) !important;
  color: white;
}

/* Form Controls */
.btn-check:checked + .btn-attendance {
  box-shadow: var(--shadow-lg);
  transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 992px) {
  .profile-section {
    padding: var(--spacing-6);
  }
  
  .profile-section h2 {
    font-size: 1.875rem;
  }
  
  .card-body {
    padding: var(--spacing-4);
  }
  
  .attendance-table th,
  .attendance-table td {
    padding: var(--spacing-3);
  }
  
  .btn-attendance {
    min-width: 80px;
    font-size: 0.8125rem;
  }
}

@media (max-width: 768px) {
  :root {
    --spacing-8: 1.5rem;
    --spacing-6: 1.25rem;
  }
  
  .profile-section {
    text-align: center;
  }
  
  .profile-section h2 {
    font-size: 1.5rem;
  }
  
  .stat-card .fs-4 {
    font-size: 1.5rem;
  }
  
  .btn-attendance {
    padding: var(--spacing-1) var(--spacing-2);
    min-width: 70px;
    font-size: 0.75rem;
  }
  
  .schedule-item:hover {
    transform: translateX(6px);
  }
  
  .subject-card:hover {
    transform: translateX(4px);
  }
}

/* Loading Animation */
@keyframes shimmer {
  0% {
    background-position: -200px 0;
  }
  100% {
    background-position: calc(200px + 100%) 0;
  }
}

.loading-shimmer {
  background: linear-gradient(90deg, var(--neutral-200) 25%, var(--neutral-100) 50%, var(--neutral-200) 75%);
  background-size: 200px 100%;
  animation: shimmer 1.5s infinite;
}

/* Floating Animation */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

.float-animation {
  animation: float 3s ease-in-out infinite;
}

/* Pulse Animation */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.pulse-animation {
  animation: pulse 2s ease-in-out infinite;
}

/* Glow Effect */
.glow-effect {
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

/* Success/Error States */
.success-state {
  background: var(--success-50);
  border-color: var(--success-200);
  color: var(--success-800);
}

.error-state {
  background: var(--error-50);
  border-color: var(--error-200);
  color: var(--error-800);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: var(--neutral-100);
  border-radius: var(--radius-lg);
}

::-webkit-scrollbar-thumb {
  background: var(--gradient-primary);
  border-radius: var(--radius-lg);
}

::-webkit-scrollbar-thumb:hover {
  background: var(--primary-600);
}

/* Focus States */
.btn:focus,
.form-control:focus,
.form-select:focus {
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  outline: none;
}

/* Print Styles */
@media print {
  .navbar-custom,
  .quick-actions,
  .present-all-btn {
    display: none !important;
  }
  
  .card {
    box-shadow: none !important;
    border: 1px solid var(--neutral-300) !important;
  }
  
  .profile-section {
    background: white !important;
    color: black !important;
  }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
  :root {
    --neutral-50: #1e293b;
    --neutral-100: #334155;
    --neutral-200: #475569;
    --neutral-800: #f1f5f9;
    --neutral-900: #f8fafc;
  }
  
  body {
    background: linear-gradient(135deg, var(--neutral-900) 0%, var(--neutral-800) 100%);
    color: var(--neutral-100);
  }
  
  .card {
    background: rgba(30, 41, 59, 0.8);
    border-color: rgba(255, 255, 255, 0.1);
  }
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .card {
    border: 2px solid var(--neutral-800);
  }
  
  .btn-attendance {
    border-width: 3px;
  }
}
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                AttendanceHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bulk-upload.php">
                            <i class="fas fa-upload me-2"></i>Bulk Upload
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
                            <li><a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">
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
                        <i class="fas fa-building me-2"></i>Department: <?php echo htmlspecialchars($_SESSION['user_department']); ?>
                    </p>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-calendar me-2"></i>Today: <?php echo date('l, F j, Y'); ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="fs-5 fw-bold"><?php echo date('h:i A'); ?></div>
                    <div class="opacity-75">Current Time</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-lg-3 mb-4">
                <!-- Subjects List -->
                <div class="card mb-4">
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
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($subject['subject_code']); ?> | 
                                                    Sem <?php echo $subject['semester']; ?>
                                                </small>
                                            </div>
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Today's Schedule
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($today_schedule)): ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <p class="mb-0">No classes scheduled for today.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($today_schedule as $class): ?>
                                <div class="schedule-item">
                                    <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($class['subject_name']); ?></h6>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('g:i A', strtotime($class['start_time'])); ?> - 
                                        <?php echo date('g:i A', strtotime($class['end_time'])); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        Room: <?php echo htmlspecialchars($class['room_no']); ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <?php if ($selected_subject): ?>
                    <!-- Attendance Marking -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-clipboard-check me-2"></i>
                                        Mark Attendance - <?php echo htmlspecialchars($selected_subject['subject_name']); ?>
                                    </h5>
                                    <small class="opacity-75">
                                        Subject Code: <?php echo htmlspecialchars($selected_subject['subject_code']); ?> | 
                                        Date: <?php echo date('Y-m-d'); ?>
                                    </small>
                                </div>
                                <?php if (!empty($students)): ?>
                                    <button type="button" class="btn present-all-btn" onclick="markAllPresent()">
                                        <i class="fas fa-check-double me-2"></i>Mark All Present
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($students)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h5>No students found</h5>
                                    <p>No students are enrolled in this subject's department and semester.</p>
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
                                                    <th>Semester</th>
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
                                                            <span class="badge bg-secondary">
                                                                Sem <?php echo $student['semester']; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <input type="radio" class="btn-check" 
                                                                       name="attendance[<?php echo $student['id']; ?>]" 
                                                                       id="present_<?php echo $student['id']; ?>" 
                                                                       value="P" 
                                                                       <?php echo ($student['today_status'] == 'P') ? 'checked' : ''; ?>>
                                                                <label class="btn btn-outline-success btn-attendance btn-present" 
                                                                       for="present_<?php echo $student['id']; ?>">
                                                                    <i class="fas fa-check me-1"></i>Present
                                                                </label>
                                                                
                                                                <input type="radio" class="btn-check" 
                                                                       name="attendance[<?php echo $student['id']; ?>]" 
                                                                       id="absent_<?php echo $student['id']; ?>" 
                                                                       value="A" 
                                                                       <?php echo ($student['today_status'] == 'A') ? 'checked' : ''; ?>>
                                                                <label class="btn btn-outline-danger btn-attendance btn-absent" 
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
                    <!-- Welcome Message and Quick Actions -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card text-center">
                                <div class="card-body py-5">
                                    <i class="fas fa-clipboard-list fa-5x text-primary mb-4"></i>
                                    <h3 class="card-title">Select a Subject</h3>
                                    <p class="card-text text-muted">
                                        Choose a subject from the left panel to start marking attendance for your students.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="quick-actions">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                                <a href="reports.php" class="action-btn">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    <div class="fw-bold">View Reports</div>
                                    <small class="opacity-75">Generate attendance reports</small>
                                </a>
                                <a href="bulk-upload.php" class="action-btn">
                                    <i class="fas fa-upload me-2"></i>
                                    <div class="fw-bold">Bulk Upload</div>
                                    <small class="opacity-75">Import student data</small>
                                </a>
                                <a href="students.php" class="action-btn">
                                    <i class="fas fa-users me-2"></i>
                                    <div class="fw-bold">Manage Students</div>
                                    <small class="opacity-75">View and edit student list</small>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <?php if (!empty($statistics)): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="fas fa-chart-bar me-2"></i>Subject-wise Statistics
                            </h5>
                        </div>
                        <?php foreach ($statistics as $stat): ?>
                            <div class="col-lg-6 col-xl-4 mb-3">
                                <div class="stat-card">
                                    <h6 class="fw-bold mb-3"><?php echo htmlspecialchars($stat['subject_name']); ?></h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="fs-4 fw-bold text-primary"><?php echo $stat['total_students']; ?></div>
                                            <small class="text-muted">Students</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fs-4 fw-bold text-success"><?php echo $stat['present_count']; ?></div>
                                            <small class="text-muted">Present</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fs-4 fw-bold text-danger"><?php echo $stat['absent_count']; ?></div>
                                            <small class="text-muted">Absent</small>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted">Total Classes: <?php echo $stat['total_classes']; ?></small>
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
        // Mark all students present
        function markAllPresent() {
            const presentRadios = document.querySelectorAll('input[type="radio"][value="P"]');
            presentRadios.forEach(radio => {
                radio.checked = true;
            });
        }

        // Attendance form submission
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
                            showAlert('success', data.message);
                        } else {
                            showAlert('danger', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', 'An error occurred while saving attendance.');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('attendanceForm');
            form.insertBefore(alertDiv, form.firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Update time every second
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            const timeElement = document.querySelector('.profile-section .fs-5');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        setInterval(updateTime, 1000);
    </script>
</body>
</html>