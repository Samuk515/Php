<?php
require_once 'config.php';

// Check session timeout
if (checkSessionTimeout()) {
    setFlashMessage('warning', 'Your session has expired. Please login again.');
    header("Location: student_login.php");
    exit();
}

// Require student authentication
requireStudent();

// Refresh session time
refreshSessionTime();

// Get student information
$student_id = $_SESSION['student_id'];
$student = getStudentByStudentId($conn, $student_id);

if (!$student) {
    setFlashMessage('danger', 'Student record not found. Please contact administration.');
    header("Location: student_login.php");
    exit();
}

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #3E2E23 0%, #5A4233 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            border: 4px solid rgba(255,255,255,0.3);
        }
        
        .profile-info h2 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .profile-info p {
            opacity: 0.9;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .profile-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .profile-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .profile-card h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .profile-card h3 i {
            color: #5A4233;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .info-item {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #3E2E23;
        }
        
        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        
        .status-active {
            color: #28a745;
        }
        
        .status-inactive {
            color: #dc3545;
        }
        
        .status-graduated {
            color: #17a2b8;
        }
        
        .status-suspended {
            color: #ffc107;
        }
        
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar Navigation -->
        <nav class="sidebar" role="navigation" aria-label="Main menu">
            <img src="../Project/harvard.jpeg" height="100" width="100" alt="Harvard University Logo">
            <h1>Harvard University</h1>
            <h2>Student Portal</h2>
            <ul class="menu">
                <li><a href="student_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="active"><a href="student_profile.php" aria-current="page"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="student_attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
                <li><a href="student_fees.php"><i class="fas fa-dollar-sign"></i> Fees</a></li>
                <li><a href="student_performance.php"><i class="fas fa-chart-line"></i> Performance</a></li>
                <li><a href="student_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <!-- Topbar -->
            <header class="topbar">
                <h2><i class="fas fa-user"></i> My Profile</h2>
                <div>
                    <span style="margin-right: 15px; color: #666;">
                        <i class="fas fa-clock"></i> <?php echo date('h:i A'); ?>
                    </span>
                    <a class="btn btn-secondary" href="student_logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </header>

            <!-- Dashboard Main -->
            <section class="dashboard" role="main">
                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'); ?>"></i>
                        <?php echo $flash['message']; ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                        <p><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($student['student_id']); ?></p>
                        <p><i class="fas fa-book"></i> <?php echo htmlspecialchars($student['course'] ?? 'No course assigned'); ?></p>
                        <span class="profile-badge">
                            <i class="fas fa-circle"></i> <?php echo $student['status']; ?>
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                    <!-- Personal Information -->
                    <div class="profile-card">
                        <h3><i class="fas fa-user"></i> Personal Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">First Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['first_name']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Last Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['last_name']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Phone</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value"><?php echo $student['date_of_birth'] ? date('M d, Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Gender</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['gender']); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="profile-card">
                        <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                        <div class="info-grid">
                            <div class="info-item" style="grid-column: span 2;">
                                <div class="info-label">Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['address'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">City</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['city'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">State/Province</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['state'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Zip/Postal Code</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['zip_code'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="profile-card">
                        <h3><i class="fas fa-graduation-cap"></i> Academic Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Student ID</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['student_id']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Course/Program</div>
                                <div class="info-value"><?php echo htmlspecialchars($student['course'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Enrollment Date</div>
                                <div class="info-value"><?php echo $student['enrollment_date'] ? date('M d, Y', strtotime($student['enrollment_date'])) : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Status</div>
                                <div class="info-value status-<?php echo strtolower($student['status']); ?>">
                                    <i class="fas fa-circle"></i> <?php echo $student['status']; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fees Information -->
                    <div class="profile-card">
                        <h3><i class="fas fa-dollar-sign"></i> Fees Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Total Fees</div>
                                <div class="info-value">$<?php echo number_format($student['fees_total'], 2); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Fees Paid</div>
                                <div class="info-value" style="color: #28a745;">$<?php echo number_format($student['fees_paid'], 2); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Fees Pending</div>
                                <div class="info-value" style="color: <?php echo $student['fees_pending'] > 0 ? '#dc3545' : '#28a745'; ?>;">
                                    $<?php echo number_format($student['fees_pending'], 2); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Payment Status</div>
                                <div class="info-value">
                                    <?php if ($student['fees_pending'] <= 0): ?>
                                        <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Paid</span>
                                    <?php else: ?>
                                        <span style="color: #dc3545;"><i class="fas fa-exclamation-circle"></i> Pending</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($student['fees_pending'] > 0): ?>
                        <div style="margin-top: 20px;">
                            <a href="student_fees.php" class="btn btn-success" style="width: 100%; justify-content: center;">
                                <i class="fas fa-credit-card"></i> Pay Fees Now
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
