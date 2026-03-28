<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: index.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid form submission. Please try again.";
    } else {
        $username = trim($_POST['username']);

        if (empty($username)) {
            $error = "Please enter username.";
        } else {
        $query = "SELECT * FROM users WHERE username = ?";
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
            
            // Redirect based on role
            if ($user['role'] === 'admin' || $user['role'] === 'teacher') {
                header("Location: index.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username.";
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
    <title>Login - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Harvard University</h1>
                <p>Student Information System</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
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

                <button type="submit" class="btn" style="width: 100%; justify-content: center; padding: 12px;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                <p style="margin-top: 15px;">
                    <a href="student_login.php" style="color: #3E2E23; text-decoration: none; font-size: 14px;">
                        <i class="fas fa-user-graduate"></i> Student Portal Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
