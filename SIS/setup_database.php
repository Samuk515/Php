<?php
// Complete database setup script
echo "<h1>Harvard University SIS - Database Setup</h1>";

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'harvard_sis';

// Connect to MySQL (without database)
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("<p style='color: red;'>✗ Connection failed: " . mysqli_connect_error() . "</p>");
}
echo "<p style='color: green;'>✓ Connected to MySQL</p>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $db";
if (mysqli_query($conn, $sql)) {
    echo "<p style='color: green;'>✓ Database '$db' created or already exists</p>";
} else {
    die("<p style='color: red;'>✗ Error creating database: " . mysqli_error($conn) . "</p>");
}

// Select database
mysqli_select_db($conn, $db);
echo "<p style='color: green;'>✓ Database selected</p>";

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('admin', 'teacher', 'student') DEFAULT 'student',
    student_id VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "<p style='color: green;'>✓ Users table created or already exists</p>";
} else {
    die("<p style='color: red;'>✗ Error creating users table: " . mysqli_error($conn) . "</p>");
}

// Create students table
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(10),
    course VARCHAR(100),
    enrollment_date DATE,
    status ENUM('Active', 'Inactive', 'Graduated', 'Suspended') DEFAULT 'Active',
    photo VARCHAR(255) DEFAULT 'default.png',
    fees_total DECIMAL(10, 2) DEFAULT 0.00,
    fees_paid DECIMAL(10, 2) DEFAULT 0.00,
    fees_pending DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "<p style='color: green;'>✓ Students table created or already exists</p>";
} else {
    die("<p style='color: red;'>✗ Error creating students table: " . mysqli_error($conn) . "</p>");
}

// Create notices table
$sql = "CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    target_audience ENUM('all', 'students', 'teachers') DEFAULT 'all',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
)";
if (mysqli_query($conn, $sql)) {
    echo "<p style='color: green;'>✓ Notices table created or already exists</p>";
} else {
    die("<p style='color: red;'>✗ Error creating notices table: " . mysqli_error($conn) . "</p>");
}

// Generate password hashes
$admin_hash = password_hash('1234', PASSWORD_DEFAULT);
$teacher_hash = password_hash('1234', PASSWORD_DEFAULT);
$student_hash = password_hash('1234', PASSWORD_DEFAULT);

echo "<hr>";
echo "<h2>Updating Passwords</h2>";

// Check if admin user exists
$query = "SELECT id FROM users WHERE username = 'admin'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    // Update existing admin
    $query = "UPDATE users SET password = ? WHERE username = 'admin'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $admin_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Admin password updated</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to update admin password: " . mysqli_error($conn) . "</p>";
    }
} else {
    // Insert new admin
    $query = "INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, 'System Administrator', 'admin@harvard.edu', 'admin')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", 'admin', $admin_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Admin user created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create admin user: " . mysqli_error($conn) . "</p>";
    }
}

// Check if teacher user exists
$query = "SELECT id FROM users WHERE username = 'teacher1'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    // Update existing teacher
    $query = "UPDATE users SET password = ? WHERE username = 'teacher1'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $teacher_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Teacher password updated</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to update teacher password: " . mysqli_error($conn) . "</p>";
    }
} else {
    // Insert new teacher
    $query = "INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, 'John Smith', 'john.smith@harvard.edu', 'teacher')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", 'teacher1', $teacher_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Teacher user created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create teacher user: " . mysqli_error($conn) . "</p>";
    }
}

// Check if student users exist
$query = "SELECT id FROM users WHERE username IN ('alice', 'bob', 'carol', 'david', 'eva')";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    // Update existing students
    $query = "UPDATE users SET password = ? WHERE username IN ('alice', 'bob', 'carol', 'david', 'eva')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $student_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Student passwords updated</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to update student passwords: " . mysqli_error($conn) . "</p>";
    }
} else {
    // Insert new students
    $query = "INSERT INTO users (username, password, full_name, email, role, student_id) VALUES (?, ?, 'Alice Johnson', 'alice.johnson@harvard.edu', 'student', 'STU001')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", 'alice', $student_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Student 'alice' created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create student 'alice': " . mysqli_error($conn) . "</p>";
    }
    
    $query = "INSERT INTO users (username, password, full_name, email, role, student_id) VALUES (?, ?, 'Bob Williams', 'bob.williams@harvard.edu', 'student', 'STU002')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", 'bob', $student_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Student 'bob' created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create student 'bob': " . mysqli_error($conn) . "</p>";
    }
    
    $query = "INSERT INTO users (username, password, full_name, email, role, student_id) VALUES (?, ?, 'Carol Davis', 'carol.davis@harvard.edu', 'student', 'STU003')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", 'carol', $student_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Student 'carol' created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create student 'carol': " . mysqli_error($conn) . "</p>";
    }
    
    $query = "INSERT INTO users (username, password, full_name, email, role, student_id) VALUES (?, ?, 'David Brown', 'david.brown@harvard.edu', 'student', 'STU004')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", 'david', $student_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Student 'david' created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create student 'david': " . mysqli_error($conn) . "</p>";
    }
    
    $query = "INSERT INTO users (username, password, full_name, email, role, student_id) VALUES (?, ?, 'Eva Martinez', 'eva.martinez@harvard.edu', 'student', 'STU005')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", 'eva', $student_hash);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Student 'eva' created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create student 'eva': " . mysqli_error($conn) . "</p>";
    }
}

// Insert sample students if they don't exist
$query = "SELECT id FROM students WHERE student_id = 'STU001'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    $query = "INSERT INTO students (student_id, first_name, last_name, email, phone, date_of_birth, gender, address, city, state, zip_code, course, enrollment_date, status, fees_total, fees_paid, fees_pending) VALUES
    ('STU001', 'Alice', 'Johnson', 'alice.johnson@harvard.edu', '9841234567', '2000-05-15', 'Female', '123 Main Street', 'Boston', 'MA', '02138', 'Computer Science', '2024-01-15', 'Active', 50000.00, 30000.00, 20000.00),
    ('STU002', 'Bob', 'Williams', 'bob.williams@harvard.edu', '9841234568', '2001-08-22', 'Male', '456 Oak Avenue', 'Cambridge', 'MA', '02139', 'Data Science', '2024-01-20', 'Active', 50000.00, 50000.00, 0.00),
    ('STU003', 'Carol', 'Davis', 'carol.davis@harvard.edu', '9841234569', '1999-12-10', 'Female', '789 Pine Road', 'Boston', 'MA', '02140', 'Software Engineering', '2024-02-01', 'Active', 50000.00, 25000.00, 25000.00),
    ('STU004', 'David', 'Brown', 'david.brown@harvard.edu', '9841234570', '2000-03-25', 'Male', '321 Elm Street', 'Cambridge', 'MA', '02141', 'Information Technology', '2024-02-10', 'Active', 50000.00, 40000.00, 10000.00),
    ('STU005', 'Eva', 'Martinez', 'eva.martinez@harvard.edu', '9841234571', '2001-07-18', 'Female', '654 Maple Lane', 'Boston', 'MA', '02142', 'Computer Science', '2024-02-15', 'Active', 50000.00, 50000.00, 0.00)";
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green;'>✓ Sample students created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create sample students: " . mysqli_error($conn) . "</p>";
    }
}

// Insert sample notices if they don't exist
$query = "SELECT id FROM notices LIMIT 1";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    $query = "INSERT INTO notices (title, content, target_audience, created_by) VALUES
    ('BCA 3rd Semester Exams Starting', 'The BCA 3rd semester examinations will commence from Baisakh 14. All students are advised to collect their admit cards from the administration office.', 'students', 1),
    ('BSc CSIT First Term Result Published', 'The first term results for BSc CSIT have been published. Students can check their results on the university portal.', 'students', 1),
    ('BHM Practical Exam', 'The practical examinations for BHM program will be held from Baisakh 10. Students must bring their lab manuals.', 'students', 1),
    ('BCA 4th Semester Practical Exam', 'Practical examinations for BCA 4th semester will begin from Baisakh 25. Contact your department for the schedule.', 'students', 1),
    ('Faculty Meeting', 'A faculty meeting is scheduled for Chaitra 30 at 2:00 PM in the conference room. All teachers are required to attend.', 'teachers', 1)";
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green;'>✓ Sample notices created</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create sample notices: " . mysqli_error($conn) . "</p>";
    }
}

echo "<hr>";
echo "<h2>Setup Complete!</h2>";
echo "<p><strong>Login Credentials:</strong></p>";
echo "<p><strong>Admin Login:</strong> <a href='login.php'>http://localhost/SIS/login.php</a></p>";
echo "<ul>";
echo "<li>Username: admin</li>";
echo "<li>Password: 1234</li>";
echo "</ul>";
echo "<p><strong>Student Login:</strong> <a href='student_login.php'>http://localhost/SIS/student_login.php</a></p>";
echo "<ul>";
echo "<li>Username: alice, bob, carol, david, or eva</li>";
echo "<li>Password: 1234</li>";
echo "</ul>";
echo "<p><strong>Teacher Login:</strong> <a href='login.php'>http://localhost/SIS/login.php</a></p>";
echo "<ul>";
echo "<li>Username: teacher1</li>";
echo "<li>Password: 1234</li>";
echo "</ul>";
echo "<hr>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>
