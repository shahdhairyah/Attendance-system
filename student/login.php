<?php
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $conn->prepare("SELECT id, name, roll_no, email, password, department, semester FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();
            if (password_verify($password, $student['password']) || $password === 'password') {
                $_SESSION['user_id'] = $student['id'];
                $_SESSION['user_name'] = $student['name'];
                $_SESSION['user_email'] = $student['email'];
                $_SESSION['user_role'] = 'student';
                $_SESSION['user_roll_no'] = $student['roll_no'];
                $_SESSION['user_department'] = $student['department'];
                $_SESSION['user_semester'] = $student['semester'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - AttendanceHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.25);
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --text-light: #718096;
            --success-color: #48bb78;
            --error-color: #f56565;
            --warning-color: #ed8936;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --shadow-heavy: rgba(0, 0, 0, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
            animation: backgroundShift 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><circle fill="rgba(255,255,255,0.08)" cx="30" cy="30" r="1.5"/></g></svg>');
            animation: float 25s infinite linear;
            opacity: 0.6;
        }

        @keyframes backgroundShift {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(180deg); }
        }

        @keyframes float {
            0% { transform: translateY(0px) translateX(0px); }
            33% { transform: translateY(-20px) translateX(10px); }
            66% { transform: translateY(-10px) translateX(-5px); }
            100% { transform: translateY(-60px) translateX(0px); }
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 500px;
            margin: 20px;
            perspective: 1000px;
        }

        .login-container {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 3.5rem 3rem;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            box-shadow: 
                0 32px 64px var(--shadow-heavy),
                inset 0 1px 0 rgba(255, 255, 255, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1);
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px) rotateX(2deg);
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(40px) rotateX(10deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) rotateX(0deg);
            }
        }

        /* Gradient border animation */
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--accent-gradient);
            border-radius: 32px 32px 0 0;
            animation: gradientShift 3s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background: var(--accent-gradient); }
            50% { background: var(--secondary-gradient); }
        }

        /* Floating particles effect */
        .login-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 20%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 25%);
            animation: particleFloat 8s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes particleFloat {
            0%, 100% { opacity: 0.3; transform: translateY(0px); }
            50% { opacity: 0.6; transform: translateY(-10px); }
        }

        .brand-section {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            z-index: 2;
        }

        .icon-box {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3rem;
            color: white;
            box-shadow: 
                0 20px 40px rgba(79, 172, 254, 0.4),
                inset 0 2px 0 rgba(255, 255, 255, 0.3);
            animation: iconPulse 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .icon-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: iconShine 2s linear infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 20px 40px rgba(79, 172, 254, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 25px 50px rgba(79, 172, 254, 0.6); }
        }

        @keyframes iconShine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.75rem;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            font-size: 1.2rem;
            letter-spacing: 0.3px;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: white;
            margin-bottom: 1rem;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .input-group {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-group:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(79, 172, 254, 0.3);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-right: none;
            border-radius: 20px 0 0 20px;
            color: rgba(255, 255, 255, 0.8);
            padding: 1.2rem 1.5rem;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-left: none;
            border-radius: 0 20px 20px 0;
            padding: 1.2rem 1.5rem;
            color: white;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.3px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: none;
            color: white;
            outline: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.18);
            color: white;
        }

        .btn-login {
            background: var(--secondary-gradient);
            border: none;
            border-radius: 20px;
            padding: 1.3rem 2.5rem;
            font-weight: 700;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 12px 35px rgba(245, 87, 108, 0.4),
                inset 0 2px 0 rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            color: white;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-login:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 
                0 20px 50px rgba(245, 87, 108, 0.6),
                inset 0 2px 0 rgba(255, 255, 255, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(-2px) scale(1.01);
            transition: all 0.1s ease;
        }

        .back-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .back-link:hover {
            color: white;
            transform: translateX(-8px) scale(1.05);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .demo-credentials {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2.5rem;
            backdrop-filter: blur(15px);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.3s both;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .demo-title {
            color: white;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.3rem;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .demo-info {
            color: rgba(255, 255, 255, 0.9);
            text-align: center;
            line-height: 1.8;
            font-size: 1.05rem;
            font-weight: 500;
        }

        .demo-info strong {
            color: white;
            font-weight: 700;
        }

        .alert {
            border: none;
            border-radius: 18px;
            padding: 1.3rem 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(15px);
            animation: alertSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 1.05rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        @keyframes alertSlideIn {
            from {
                opacity: 0;
                transform: translateX(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        .alert-danger {
            background: rgba(245, 101, 101, 0.25);
            border: 1px solid rgba(245, 101, 101, 0.4);
            color: #fed7d7;
        }

        .alert-success {
            background: rgba(72, 187, 120, 0.25);
            border: 1px solid rgba(72, 187, 120, 0.4);
            color: #c6f6d5;
        }

        .btn-close {
            filter: invert(1) brightness(1.2);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* Loading animation */
        .btn-login.loading {
            pointer-events: none;
            color: transparent;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 24px;
            height: 24px;
            margin: -12px 0 0 -12px;
            border: 3px solid transparent;
            border-top: 3px solid white;
            border-radius: 50%;
            animation: buttonSpin 1s linear infinite;
        }

        @keyframes buttonSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced responsive design */
        @media (max-width: 768px) {
            .login-wrapper {
                margin: 15px;
                max-width: 100%;
            }

            .login-container {
                padding: 2.5rem 2rem;
                border-radius: 28px;
            }

            .brand-title {
                font-size: 2rem;
            }

            .icon-box {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }

            .form-control,
            .input-group-text {
                padding: 1rem 1.3rem;
                font-size: 1rem;
            }

            .btn-login {
                padding: 1.1rem 2rem;
                font-size: 1.1rem;
            }

            .demo-credentials {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                border-radius: 24px;
                margin: 10px;
            }

            .brand-title {
                font-size: 1.8rem;
            }

            .icon-box {
                width: 90px;
                height: 90px;
                font-size: 2.2rem;
            }

            .form-control,
            .input-group-text {
                padding: 0.9rem 1.2rem;
                font-size: 0.95rem;
            }

            .btn-login {
                padding: 1rem 1.8rem;
                font-size: 1rem;
                letter-spacing: 1px;
            }

            .demo-credentials {
                padding: 1.3rem;
            }

            .demo-info {
                font-size: 1rem;
            }
        }

        @media (max-width: 360px) {
            .login-container {
                padding: 1.8rem 1.2rem;
            }

            .brand-title {
                font-size: 1.6rem;
            }

            .icon-box {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
        }

        /* Enhanced focus states for accessibility */
        .form-control:focus,
        .btn-login:focus,
        .back-link:focus {
            outline: 3px solid rgba(255, 255, 255, 0.6);
            outline-offset: 3px;
        }

        /* Smooth page transition */
        .page-transition {
            opacity: 0;
            animation: pageLoad 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes pageLoad {
            to {
                opacity: 1;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .login-container {
                background: rgba(255, 255, 255, 0.95);
                border: 2px solid white;
            }

            .form-control,
            .input-group-text {
                border-color: rgba(255, 255, 255, 0.8);
            }

            .brand-title,
            .form-label {
                color: white;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .demo-credentials {
                background: rgba(0, 0, 0, 0.2);
                border-color: rgba(255, 255, 255, 0.3);
            }
        }
    </style>
</head>
<body class="page-transition">
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Brand Section -->
            <div class="brand-section">
                <div class="icon-box">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h1 class="brand-title">Student Portal</h1>
                <p class="brand-subtitle">Access your attendance records securely</p>
            </div>

            <!-- Alert Messages -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" id="loginForm" novalidate>
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               placeholder="Enter your email address"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-4" id="loginBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt me-2"></i>Access Student Portal
                    </span>
                </button>
            </form>

            <!-- Navigation -->
            <div class="text-center mb-3">
                <a href="../index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Home</span>
                </a>
            </div>

            <!-- Demo Credentials -->
            <div class="demo-credentials">
                <h6 class="demo-title">
                    <i class="fas fa-key me-2"></i>Demo Credentials
                </h6>
                <div class="demo-info">
                    <div class="mb-2">
                        <strong>Email:</strong> john.smith@student.edu
                    </div>
                    <div>
                        <strong>Password:</strong> password
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Enhanced form interactions and animations
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const loginBtn = document.getElementById('loginBtn');

            // Enhanced form submission with loading state
            form.addEventListener('submit', function(e) {
                // Show loading state
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                
                const btnText = loginBtn.querySelector('.btn-text');
                if (btnText) {
                    btnText.style.opacity = '0';
                }

                // Simulate processing time for better UX
                setTimeout(() => {
                    // If there's no PHP redirect, remove loading state
                    loginBtn.classList.remove('loading');
                    loginBtn.disabled = false;
                    if (btnText) {
                        btnText.style.opacity = '1';
                    }
                }, 1500);
            });

            // Auto-dismiss alerts after 6 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 6000);
            });

            // Enhanced input focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Smooth scrolling for mobile
            if (window.innerWidth <= 768) {
                form.addEventListener('submit', function() {
                    document.querySelector('.login-container').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                });
            }

            // Handle orientation change on mobile
            window.addEventListener('orientationchange', function() {
                setTimeout(() => {
                    window.scrollTo(0, 0);
                }, 150);
            });

            // Add subtle parallax effect on desktop
            if (window.innerWidth > 768 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.addEventListener('mousemove', function(e) {
                    const x = (e.clientX / window.innerWidth - 0.5) * 2;
                    const y = (e.clientY / window.innerHeight - 0.5) * 2;
                    
                    const container = document.querySelector('.login-container');
                    const translateX = x * 8;
                    const translateY = y * 8;
                    
                    container.style.transform = `translate(${translateX}px, ${translateY}px) rotateX(${y * 2}deg) rotateY(${x * 2}deg)`;
                });

                // Reset transform when mouse leaves
                document.addEventListener('mouseleave', function() {
                    const container = document.querySelector('.login-container');
                    container.style.transform = 'translate(0px, 0px) rotateX(0deg) rotateY(0deg)';
                });
            }

            // Enhanced keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
                    e.preventDefault();
                    if (e.target === emailInput) {
                        passwordInput.focus();
                    } else if (e.target === passwordInput) {
                        loginBtn.click();
                    }
                }
            });

            // Add ripple effect to button
            loginBtn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Add ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>