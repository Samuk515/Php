<?php
require_once 'config.php';
requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit();
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM students WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    setFlashMessage('danger', 'Student not found.');
    header("Location: students.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - Harvard University SIS</title>
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
                <div>
                    <a class="btn btn-secondary" href="students.php"><i class="fas fa-arrow-left"></i> Back to List</a>
                    <a class="btn" href="edit_student.php?id=<?php echo $student['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                    <a class="btn btn-danger" href="delete_student.php?id=<?php echo $student['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this student?');">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                    <a class="btn btn-secondary" href="logout.php" style="margin-left: 10px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </header>

            <!-- Dashboard Main -->
            <section class="dashboard" role="main">
                <div class="header">
                    <h1>Student Details</h1>
                    <p>Home / Students / <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                </div>

                <div class="table-box">
                    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                        <!-- Photo Section -->
                        <div style="text-align: center; min-width: 200px;">
                            <div style="width: 150px; height: 150px; background: #3E2E23; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: white; font-size: 48px; font-weight: bold;">
                                <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
                            </div>
                            <h3 style="color: #333; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                            </h3>
                            <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                                <?php echo htmlspecialchars($student['student_id']); ?>
                            </p>
                            <span class="badge badge-<?php echo strtolower($student['status']); ?>">
                                <?php echo $student['status']; ?>
                            </span>
                        </div>

                        <!-- Information Section -->
                        <div style="flex: 1;">
                            <!-- Personal Information -->
                            <div style="margin-bottom: 30px;">
                                <h3 style="color: #1a237e; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0;">
                                    <i class="fas fa-user"></i> Personal Information
                                </h3>
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">First Name</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['first_name']); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Last Name</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['last_name']); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Email</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Phone</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Date of Birth</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo $student['date_of_birth'] ? date('M d, Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Gender</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['gender']); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div style="margin-bottom: 30px;">
                                <h3 style="color: #1a237e; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0;">
                                    <i class="fas fa-map-marker-alt"></i> Address Information
                                </h3>
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                                    <div style="grid-column: span 2;">
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Address</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['address'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">City</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['city'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">State/Province</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['state'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Zip/Postal Code</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['zip_code'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div style="margin-bottom: 30px;">
                                <h3 style="color: #1a237e; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0;">
                                    <i class="fas fa-graduation-cap"></i> Academic Information
                                </h3>
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Student ID</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['student_id']); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Course/Program</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($student['course'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Enrollment Date</label>
                                        <p style="font-size: 14px; color: #333;"><?php echo $student['enrollment_date'] ? date('M d, Y', strtotime($student['enrollment_date'])) : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Status</label>
                                        <p style="font-size: 14px; color: #333;">
                                            <span class="badge badge-<?php echo strtolower($student['status']); ?>">
                                                <?php echo $student['status']; ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fees Information -->
                            <div>
                                <h3 style="color: #1a237e; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0;">
                                    <i class="fas fa-dollar-sign"></i> Fees Information
                                </h3>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Total Fees</label>
                                        <p style="font-size: 14px; color: #333;">$<?php echo number_format($student['fees_total'], 2); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Fees Paid</label>
                                        <p style="font-size: 14px; color: #28a745;">$<?php echo number_format($student['fees_paid'], 2); ?></p>
                                    </div>
                                    <div>
                                        <label style="font-size: 12px; color: #999; text-transform: uppercase;">Fees Pending</label>
                                        <p style="font-size: 14px; color: <?php echo $student['fees_pending'] > 0 ? '#dc3545' : '#28a745'; ?>;">$<?php echo number_format($student['fees_pending'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
