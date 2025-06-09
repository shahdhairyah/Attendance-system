<?php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Demo credentials
    $admin_email = 'admin@collage.edu';
    $admin_password = 'password';
    
    // if ($email === $admin_email && $password === $admin_password) {
    //     $_SESSION['admin_logged_in'] = true;
    //     $_SESSION['admin_email'] = $email;
        
        if ($remember) {
            setcookie('admin_remember', base64_encode($email), time() + (30 * 24 * 60 * 60), '/');
        }
        
        // Return JSON response for AJAX
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Login successful! Redirecting to dashboard...']);
            exit;
        }
        
        $success_message = "Login successful! Redirecting...";
        echo "<script>
            setTimeout(function() {
                window.location.href = 'dashboard.php';
            }, 1500);
        </script>";
    } else {
        // Return JSON response for AJAX
        if (isset($_POST['ajax'])) {
            // header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid email or password. Please try again.']);
            exit;
        }
        
        $error_message = "Invalid email or password. Please try again.";
    }


// Check if user is already logged in
// if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
//     header('Location: dashboard.php');
//     exit;
// }

// Check for remember me cookie
$remembered_email = '';
if (isset($_COOKIE['admin_remember'])) {
    $remembered_email = base64_decode($_COOKIE['admin_remember']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - College Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --light-gray: #f8fafc;
            --medium-gray: #64748b;
            --dark-gray: #1e293b;
            --border-radius: 20px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
            animation: backgroundShift 20s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes backgroundShift {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(2deg); }
        }

        /* Floating Particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        .particle:nth-child(1) { width: 4px; height: 4px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 6px; height: 6px; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 3px; height: 3px; left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 5px; height: 5px; left: 40%; animation-delay: 6s; }
        .particle:nth-child(5) { width: 4px; height: 4px; left: 50%; animation-delay: 8s; }
        .particle:nth-child(6) { width: 7px; height: 7px; left: 60%; animation-delay: 10s; }
        .particle:nth-child(7) { width: 3px; height: 3px; left: 70%; animation-delay: 12s; }
        .particle:nth-child(8) { width: 5px; height: 5px; left: 80%; animation-delay: 14s; }
        .particle:nth-child(9) { width: 4px; height: 4px; left: 90%; animation-delay: 16s; }

        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            padding: 2rem 1rem;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--border-radius);
            padding: 3.5rem 3rem;
            box-shadow: var(--shadow-2xl);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25);
        }

        .login-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: all 0.6s ease;
        }

        .logo:hover {
            transform: scale(1.1) rotate(10deg);
            box-shadow: var(--shadow-xl);
        }

        .logo:hover::before {
            left: 100%;
        }

        .logo i {
            color: white;
            font-size: 2.2rem;
            z-index: 1;
        }

        .login-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--dark-gray);
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
            background: linear-gradient(135deg, var(--dark-gray), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            font-size: 1.1rem;
            color: var(--medium-gray);
            font-weight: 400;
            line-height: 1.6;
        }

        .form-floating {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem 1.25rem 1.25rem 3.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(248, 250, 252, 0.8);
            backdrop-filter: blur(10px);
            height: 65px;
        }

        .form-floating .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-2px);
        }

        .form-floating .form-control:not(:placeholder-shown) {
            background: white;
        }

        .form-floating label {
            padding-left: 3.5rem;
            font-weight: 600;
            color: var(--medium-gray);
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary-color);
            font-weight: 700;
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-gray);
            z-index: 10;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .form-floating .form-control:focus ~ .input-icon {
            color: var(--primary-color);
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--medium-gray);
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .password-toggle:hover {
            color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
        }

        .form-check {
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }

        .form-check-input {
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3e%3cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3e%3c/svg%3e");
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-check-label {
            font-weight: 500;
            color: var(--dark-gray);
            margin-left: 0.75rem;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-login {
            width: 100%;
            padding: 1.25rem;
            font-size: 1.1rem;
            font-weight: 700;
            background: var(--primary-gradient);
            border: none;
            border-radius: 16px;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 0.75rem;
        }

        .alert {
            border: none;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border-left: 5px solid;
            animation: slideInDown 0.5s ease-out;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-left-color: var(--success-color);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            border-left-color: var(--danger-color);
        }

        .demo-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1));
            border: 2px solid rgba(59, 130, 246, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .demo-info h6 {
            color: var(--info-color);
            font-weight: 700;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .demo-info p {
            margin: 0;
            font-size: 0.95rem;
            color: var(--medium-gray);
            font-weight: 500;
        }

        .demo-credentials {
            background: rgba(248, 250, 252, 0.9);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .forgot-password {
            text-align: center;
            margin-top: 2rem;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
            transform: translateY(-1px);
        }

        /* Animations */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .login-card {
            animation: fadeInUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .logo {
            animation: pulse 3s infinite;
        }

        /* Loading state */
        .loading .btn-login {
            pointer-events: none;
        }

        .loading .login-card {
            pointer-events: none;
        }

        /* Success state */
        .success-state .login-card {
            border-color: var(--success-color);
            background: rgba(255, 255, 255, 0.98);
        }

        .success-state .logo {
            background: var(--success-gradient);
            animation: none;
            transform: scale(1.1);
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2.5rem 2rem;
            }
            
            .login-title {
                font-size: 1.75rem;
            }
            
            .logo {
                width: 70px;
                height: 70px;
            }
            
            .logo i {
                font-size: 1.8rem;
            }
            
            .form-floating .form-control {
                padding: 1rem 1rem 1rem 3rem;
                font-size: 1rem;
                height: 60px;
            }
            
            .form-floating label {
                padding-left: 3rem;
                font-size: 0.9rem;
            }
            
            .input-icon {
                left: 1rem;
                font-size: 1rem;
            }
            
            .password-toggle {
                right: 1rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.5);
        }
    </style>
</head>
<body>
    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-container">
        <div class="login-card <?php echo isset($success_message) ? 'success-state' : ''; ?>" id="loginCard">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="login-title">Admin Portal</h1>
                <p class="login-subtitle">Welcome back! Please sign in to access your dashboard.</p>
            </div>

            <!-- Demo Information -->
            <div class="demo-info">
                <h6><i class="fas fa-info-circle me-2"></i>Demo Access</h6>
                <p>Use the credentials below to access the admin panel</p>
                <div class="demo-credentials">
                    <strong>Email:</strong> admin@collage.edu<br>
                    <strong>Password:</strong> password
                </div>
            </div>

            <!-- Alert Messages -->
            <div id="alertContainer">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Login Form -->
            <form method="POST" action="" id="loginForm" novalidate>
                <input type="hidden" name="ajax" value="1">
                
                <div class="form-floating">
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="admin@collage.edu"
                           value="<?php echo htmlspecialchars($remembered_email); ?>"
                           required>
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>

                <div class="form-floating">
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Password"
                           required>
                    <label for="password">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                    </button>
                    <div class="invalid-feedback">
                        Password is required.
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           value="1" 
                           id="remember" 
                           name="remember"
                           <?php echo !empty($remembered_email) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="remember">
                        Remember me for 30 days
                    </label>
                </div>

                <button type="submit" class="btn btn-login" id="submitBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In to Dashboard
                    </span>
                </button>
            </form>

            <div class="forgot-password">
                <a href="#" onclick="showForgotPassword()">
                    <i class="fas fa-key me-1"></i>
                    Forgot your password?
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Enhanced form validation and AJAX submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const alertContainer = document.getElementById('alertContainer');
            const loginCard = document.getElementById('loginCard');

            // Auto-fill demo credentials if empty
            if (!emailInput.value) {
                emailInput.value = 'admin@collage.edu';
                emailInput.parentElement.classList.add('focused');
            }

            // Enhanced real-time validation
            function validateField(field) {
                const isValid = field.checkValidity();
                const hasValue = field.value.length > 0;
                
                field.classList.remove('is-valid', 'is-invalid');
                
                if (hasValue) {
                    field.classList.add(isValid ? 'is-valid' : 'is-invalid');
                }
                
                return isValid;
            }

            // Add input event listeners
            emailInput.addEventListener('input', () => validateField(emailInput));
            passwordInput.addEventListener('input', () => validateField(passwordInput));

            // Enhanced form submission with AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const isEmailValid = validateField(emailInput);
                const isPasswordValid = validateField(passwordInput);

                if (!isEmailValid || !isPasswordValid) {
                    showAlert('Please fill in all required fields correctly.', 'danger');
                    return;
                }

                // Show loading state
                setLoadingState(true);

                // Prepare form data
                const formData = new FormData(form);

                // AJAX request
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        loginCard.classList.add('success-state');
                        
                        // Redirect to dashboard after success animation
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 2000);
                    } else {
                        showAlert(data.message, 'danger');
                        setLoadingState(false);
                        
                        // Add shake animation on error
                        loginCard.style.animation = 'shake 0.5s ease-in-out';
                        setTimeout(() => {
                            loginCard.style.animation = '';
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred. Please try again.', 'danger');
                    setLoadingState(false);
                });
            });

            // Loading state management
            function setLoadingState(loading) {
                if (loading) {
                    document.body.classList.add('loading');
                    submitBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Signing In...
                    `;
                    submitBtn.disabled = true;
                } else {
                    document.body.classList.remove('loading');
                    submitBtn.innerHTML = `
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Sign In to Dashboard
                        </span>
                    `;
                    submitBtn.disabled = false;
                }
            }

            // Enhanced alert system
            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type}" role="alert">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                `;
                
                alertContainer.innerHTML = alertHtml;
                
                // Auto-hide non-success alerts
                if (type !== 'success') {
                    setTimeout(() => {
                        const alert = alertContainer.querySelector('.alert');
                        if (alert) {
                            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            alert.style.opacity = '0';
                            alert.style.transform = 'translateY(-20px)';
                            setTimeout(() => alert.remove(), 500);
                        }
                    }, 5000);
                }
            }

            // Enhanced floating label animation
            document.querySelectorAll('.form-floating .form-control').forEach(input => {
                function updateFloatingLabel() {
                    const hasValue = input.value.length > 0;
                    const isFocused = document.activeElement === input;
                    
                    if (hasValue || isFocused) {
                        input.parentElement.classList.add('focused');
                    } else {
                        input.parentElement.classList.remove('focused');
                    }
                }

                input.addEventListener('focus', updateFloatingLabel);
                input.addEventListener('blur', updateFloatingLabel);
                input.addEventListener('input', updateFloatingLabel);
                
                // Initial check
                updateFloatingLabel();
            });

            // Add shake animation keyframes
            const style = document.createElement('style');
            style.textContent = `
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                    20%, 40%, 60%, 80% { transform: translateX(5px); }
                }
            `;
            document.head.appendChild(style);
        });

        // Enhanced password toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
            
            // Add a subtle animation
            toggleIcon.style.transform = 'scale(0.8)';
            setTimeout(() => {
                toggleIcon.style.transform = 'scale(1)';
            }, 150);
        }

        // Enhanced forgot password
        function showForgotPassword() {
            const message = `
                🔐 Demo Access Information
                
                For demonstration purposes, please use:
                
                📧 Email: admin@collage.edu
                🔑 Password: password
                
                In a production environment, this would redirect to a password reset page.
            `;
            
            alert(message);
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('submitBtn').click();
            }
            
            // Escape to clear form
            if (e.key === 'Escape') {
                document.getElementById('loginForm').reset();
                document.querySelectorAll('.form-control').forEach(input => {
                    input.classList.remove('is-valid', 'is-invalid');
                    input.parentElement.classList.remove('focused');
                });
            }
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // Enhanced particle animation
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
            particle.style.animationDelay = Math.random() * 2 + 's';
            
            document.querySelector('.particles').appendChild(particle);
            
            // Remove particle after animation
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 20000);
        }

        // Create particles periodically
        setInterval(createParticle, 3000);
    </script>
</body>
</html>