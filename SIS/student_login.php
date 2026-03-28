<?php
require_once 'config.php';

// Redirect if already logged in as student
if (isLoggedIn() && isStudent()) {
    header("Location: student_dashboard.php");
    exit();
}

// Session is already started in config.php

$error = '';
$success = '';

// Check for logout message
if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $success = "You have been successfully logged out.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid form submission. Please try again.";
    } else {
        $username = trim( $_POST['username'] ?? '');

        if (empty($username)) {
            $error = "Please enter username.";
        } else {
        $query = "SELECT * FROM users WHERE username = ? AND role = 'student'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['login_time'] = time();
            
            header("Location: student_dashboard.php");
            exit();
        } else {
            $error = "Invalid username. Only students can login here.";
        }
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3E2E23 0%, #5A4233 100%);
            padding: 20px;
        }
        
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo {
            width: 100px;
            height: 100px;
            background: #3E2E23;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        
        .login-header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 8px;
        }
        
        .login-header p {
            font-size: 14px;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #5A4233;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #5A4233;
            box-shadow: 0 0 0 3px rgba(90, 66, 51, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #3E2E23;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-login:hover {
            background: #5A4233;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .demo-credentials {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
        
        .demo-credentials p {
            color: #666;
            font-size: 13px;
            margin-bottom: 10px;
        }
        
        .demo-credentials code {
            background: #f5f5f5;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: monospace;
            color: #333;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #5A4233;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: #3E2E23;
        }
        
        .user-type-badge {
            display: inline-block;
            background: #3E2E23;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h1>Student Portal</h1>
                <p>Harvard University Student Information System</p>
                <span class="user-type-badge">
                    <i class="fas fa-user"></i> Student Login
                </span>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <?php echo csrfField(); ?>
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Enter your username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           required autofocus>
                </div>

                <!-- Password field removed for testing -->

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="demo-credentials">
                <p><strong>Note:</strong> Contact administration for login credentials.</p>
            </div>

            <div class="back-link">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i> Back to Main Login
                </a>
            </div>
        </div>
    </div>

    <script>
        // Add loading state to form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-login');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            btn.disabled = true;
        });

        // Auto-focus on username field
        document.getElementById('username').focus();
    </script>
</body>
</html>
