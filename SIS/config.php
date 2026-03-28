<?php
// Harvard University - Student Information System
// Database Configuration with Role-Based Access

// Configure secure session settings before starting session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

session_start();

// Database configuration - use environment variables in production
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db = getenv('DB_NAME') ?: 'harvard_sis';

// Create database connection
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("A database error occurred. Please try again later.");
}

// Set charset to UTF-8
mysqli_set_charset($conn, 'utf8');

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Check if user is admin
function isAdmin() {
    return getUserRole() === 'admin';
}

// Check if user is student
function isStudent() {
    return getUserRole() === 'student';
}

// Check if user is teacher
function isTeacher() {
    return getUserRole() === 'teacher';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: student_dashboard.php");
        exit();
    }
}

// Redirect if not student - with better error handling
function requireStudent() {
    if (!isLoggedIn()) {
        header("Location: student_login.php");
        exit();
    }
    if (!isStudent()) {
        // If logged in but not a student, redirect to appropriate dashboard
        if (isAdmin() || isTeacher()) {
            header("Location: index.php");
        } else {
            header("Location: student_login.php");
        }
        exit();
    }
}

// Redirect if not teacher
function requireTeacher() {
    requireLogin();
    if (!isTeacher()) {
        header("Location: index.php");
        exit();
    }
}

// Get current user info
function getCurrentUser($conn) {
    if (!isLoggedIn()) return null;
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Sanitize input
function sanitize($conn, $data) {
    return htmlspecialchars(trim($data));
}

// Generate unique student ID
function generateStudentId($conn) {
    $query = "SELECT MAX(CAST(SUBSTRING(student_id, 4) AS UNSIGNED)) as max_id FROM students";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $next_id = ($row['max_id'] ?? 0) + 1;
    return 'STU' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}

// Flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

// Get statistics for dashboard
function getDashboardStats($conn) {
    $stats = [];
    
    // Total students
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM students");
    $stats['total_students'] = mysqli_fetch_assoc($result)['count'];
    
    // Active students
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM students WHERE status='Active'");
    $stats['active_students'] = mysqli_fetch_assoc($result)['count'];
    
    // Total teachers
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='teacher'");
    $stats['total_teachers'] = mysqli_fetch_assoc($result)['count'];
    
    // Total courses
    $result = mysqli_query($conn, "SELECT COUNT(DISTINCT course) as count FROM students WHERE course IS NOT NULL AND course != ''");
    $stats['total_courses'] = mysqli_fetch_assoc($result)['count'];
    
    // Pending fees (example calculation)
    $result = mysqli_query($conn, "SELECT SUM(fees_pending) as total FROM students WHERE fees_pending > 0");
    $stats['pending_fees'] = mysqli_fetch_assoc($result)['total'] ?? 0;
    
    // Collected fees (example calculation)
    $result = mysqli_query($conn, "SELECT SUM(fees_paid) as total FROM students WHERE fees_paid > 0");
    $stats['collected_fees'] = mysqli_fetch_assoc($result)['total'] ?? 0;
    
    return $stats;
}

// Get notices for dashboard
function getNotices($conn, $limit = 5) {
    $query = "SELECT * FROM notices ORDER BY created_at DESC LIMIT ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $notices = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notices[] = $row;
    }
    return $notices;
}

// Get student by student_id
function getStudentByStudentId($conn, $student_id) {
    $query = "SELECT * FROM students WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Check session timeout (30 minutes)
function checkSessionTimeout() {
    if (isset($_SESSION['login_time'])) {
        $elapsed = time() - $_SESSION['login_time'];
        if ($elapsed > 1800) { // 30 minutes
            session_destroy();
            return true;
        }
    }
    return false;
}

// Refresh session time
function refreshSessionTime() {
    $_SESSION['login_time'] = time();
}

// Generate CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Get CSRF token hidden input field
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
?>
