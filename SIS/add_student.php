<?php
require_once 'config.php';
requireAdmin();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid form submission. Please try again.";
    } else {
        // Validate and sanitize inputs
        $first_name = sanitize($conn, $_POST['first_name'] ?? '');
    $last_name = sanitize($conn, $_POST['last_name'] ?? '');
    $email = sanitize($conn, $_POST['email'] ?? '');
    $phone = sanitize($conn, $_POST['phone'] ?? '');
    $date_of_birth = sanitize($conn, $_POST['date_of_birth'] ?? '');
    $gender = sanitize($conn, $_POST['gender'] ?? '');
    $address = sanitize($conn, $_POST['address'] ?? '');
    $city = sanitize($conn, $_POST['city'] ?? '');
    $state = sanitize($conn, $_POST['state'] ?? '');
    $zip_code = sanitize($conn, $_POST['zip_code'] ?? '');
    $course = sanitize($conn, $_POST['course'] ?? '');
    $enrollment_date = sanitize($conn, $_POST['enrollment_date'] ?? '');
    $status = sanitize($conn, $_POST['status'] ?? 'Active');
    $fees_total = floatval($_POST['fees_total'] ?? 0);
    $fees_paid = floatval($_POST['fees_paid'] ?? 0);

    // Validation
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($gender)) $errors[] = "Gender is required.";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check for duplicate email
    if (!empty($email)) {
        $check_email = mysqli_prepare($conn, "SELECT id FROM students WHERE email = ?");
        mysqli_stmt_bind_param($check_email, "s", $email);
        mysqli_stmt_execute($check_email);
        mysqli_stmt_store_result($check_email);
        if (mysqli_stmt_num_rows($check_email) > 0) {
            $errors[] = "A student with this email already exists.";
        }
        mysqli_stmt_close($check_email);
    }

    if (empty($errors)) {
        $student_id = generateStudentId($conn);
        $fees_pending = $fees_total - $fees_paid;
        
        $query = "INSERT INTO students (student_id, first_name, last_name, email, phone, date_of_birth, gender, address, city, state, zip_code, course, enrollment_date, status, fees_total, fees_paid, fees_pending) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssssssssdddd", 
            $student_id, $first_name, $last_name, $email, $phone, $date_of_birth, 
            $gender, $address, $city, $state, $zip_code, $course, $enrollment_date, $status,
            $fees_total, $fees_paid, $fees_pending
        );

        if (mysqli_stmt_execute($stmt)) {
            setFlashMessage('success', "Student '$first_name $last_name' has been added successfully with ID: $student_id");
            header("Location: students.php");
            exit();
        } else {
            error_log("Error adding student: " . mysqli_error($conn));
            $errors[] = "An error occurred while adding the student. Please try again later.";
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
    <title>Add Student - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="layout">
        <!-- Sidebar Navigation -->
        <nav class="sidebar" role="navigation" aria-label="Main menu">
            <img src="../Project/harvard.jpeg" height="100" width="100" alt="Harvard University Logo">
            <h1>Harvard University</h1>
            <h2>Dashboard</h2>
            <ul class="menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="active"><a href="students.php" aria-current="page"><i class="fas fa-users"></i> Students</a></li>
                <li><a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a></li>
                <li><a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
                <li><a href="subjects.php"><i class="fas fa-book"></i> Subjects</a></li>
                <li><a href="fees.php"><i class="fas fa-dollar-sign"></i> Fees</a></li>
                <li><a href="performance.php"><i class="fas fa-chart-line"></i> Performance</a></li>
            </ul>
        </nav>

        <main class="content">
            <!-- Topbar -->
            <header class="topbar">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h2>
                <a class="btn btn-secondary" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </header>

            <!-- Dashboard Main -->
            <section class="dashboard" role="main">
                <div class="header">
                    <h1>Add New Student</h1>
                    <p>Home / Students / Add Student</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <?php echo csrfField(); ?>
                    <!-- Personal Information -->
                    <div class="form-container">
                        <h2><i class="fas fa-user"></i> Personal Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name <span style="color: red;">*</span></label>
                                <input type="text" id="first_name" name="first_name" class="form-control" 
                                       placeholder="Enter first name" 
                                       value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name <span style="color: red;">*</span></label>
                                <input type="text" id="last_name" name="last_name" class="form-control" 
                                       placeholder="Enter last name" 
                                       value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       placeholder="Enter email address" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" 
                                       placeholder="Enter phone number" 
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" 
                                       value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender <span style="color: red;">*</span></label>
                                <select id="gender" name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="form-container">
                        <h2><i class="fas fa-map-marker-alt"></i> Address Information</h2>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" class="form-control" 
                                      placeholder="Enter street address" rows="3"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control" 
                                       placeholder="Enter city" 
                                       value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state" class="form-control" 
                                       placeholder="Enter state/province" 
                                       value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="zip_code">Zip/Postal Code</label>
                                <input type="text" id="zip_code" name="zip_code" class="form-control" 
                                       placeholder="Enter zip code" 
                                       value="<?php echo isset($_POST['zip_code']) ? htmlspecialchars($_POST['zip_code']) : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="form-container">
                        <h2><i class="fas fa-graduation-cap"></i> Academic Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="course">Course/Program</label>
                                <input type="text" id="course" name="course" class="form-control" 
                                       placeholder="e.g., Computer Science" 
                                       value="<?php echo isset($_POST['course']) ? htmlspecialchars($_POST['course']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="enrollment_date">Enrollment Date</label>
                                <input type="date" id="enrollment_date" name="enrollment_date" class="form-control" 
                                       value="<?php echo isset($_POST['enrollment_date']) ? htmlspecialchars($_POST['enrollment_date']) : date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="Active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="Graduated" <?php echo (isset($_POST['status']) && $_POST['status'] === 'Graduated') ? 'selected' : ''; ?>>Graduated</option>
                                    <option value="Suspended" <?php echo (isset($_POST['status']) && $_POST['status'] === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Fees Information -->
                    <div class="form-container">
                        <h2><i class="fas fa-dollar-sign"></i> Fees Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fees_total">Total Fees ($)</label>
                                <input type="number" id="fees_total" name="fees_total" class="form-control" 
                                       placeholder="Enter total fees" step="0.01" min="0"
                                       value="<?php echo isset($_POST['fees_total']) ? htmlspecialchars($_POST['fees_total']) : '50000.00'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="fees_paid">Fees Paid ($)</label>
                                <input type="number" id="fees_paid" name="fees_paid" class="form-control" 
                                       placeholder="Enter fees paid" step="0.01" min="0"
                                       value="<?php echo isset($_POST['fees_paid']) ? htmlspecialchars($_POST['fees_paid']) : '0.00'; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="form-container">
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <a href="students.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="reset" class="btn btn-warning">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Save Student
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
