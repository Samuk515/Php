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

// Get notices for students
$notices_query = "SELECT * FROM notices WHERE target_audience IN ('all', 'students') ORDER BY created_at DESC LIMIT 5";
$notices_result = mysqli_query($conn, $notices_query);
$notices = [];
while ($row = mysqli_fetch_assoc($notices_result)) {
    $notices[] = $row;
}

// Calculate fees percentage
$fees_percentage = $student['fees_total'] > 0 ? round(($student['fees_paid'] / $student['fees_total']) * 100) : 0;

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, #3E2E23 0%, #5A4233 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .welcome-text h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .welcome-text p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .student-id-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .fees-progress {
            margin-top: 15px;
        }
        
        .progress-bar {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: #28a745;
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .quick-action-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #3E2E23;
        }
        
        .quick-action-card i {
            font-size: 32px;
            color: #3E2E23;
            margin-bottom: 10px;
        }
        
        .quick-action-card h3 {
            font-size: 14px;
            margin: 0;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }
        
        .info-card h3 {
            color: #3E2E23;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 16px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #666;
            font-size: 14px;
        }
        
        .info-value {
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .status-active {
            color: #28a745;
        }
        
        .status-inactive {
            color: #dc3545;
        }
        
        .notice-item {
            padding: 15px;
            border-left: 4px solid #3E2E23;
            background: #f9f9f9;
            margin-bottom: 15px;
            border-radius: 0 5px 5px 0;
        }
        
        .notice-item h4 {
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .notice-item p {
            color: #666;
            font-size: 13px;
            margin: 0;
        }
        
        .notice-date {
            color: #999;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .session-info {
            background: #fff3cd;
            color: #856404;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        @media (max-width: 768px) {
            .welcome-banner {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
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
                <li class="active"><a href="student_dashboard.php" aria-current="page"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="student_attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
                <li><a href="student_fees.php"><i class="fas fa-dollar-sign"></i> Fees</a></li>
                <li><a href="student_performance.php"><i class="fas fa-chart-line"></i> Performance</a></li>
                <li><a href="student_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <!-- Topbar -->
            <header class="topbar">
                <h2><i class="fas fa-user-graduate"></i> Student Portal</h2>
                <div>
                    <span style="margin-right: 15px; color: #666;">
                        <i class="fas fa-clock"></i> Session: <?php echo date('h:i A'); ?>
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

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-text">
                        <h2>Welcome back, <?php echo htmlspecialchars($student['first_name']); ?>!</h2>
                        <p>Here's what's happening with your academic profile today.</p>
                    </div>
                    <div style="text-align: right;">
                        <div class="student-id-badge">
                            <i class="fas fa-id-card"></i> <?php echo htmlspecialchars($student['student_id']); ?>
                        </div>
                        <div class="fees-progress">
                            <small>Fee Payment: <?php echo $fees_percentage; ?>%</small>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $fees_percentage; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="student_profile.php" class="quick-action-card">
                        <i class="fas fa-user"></i>
                        <h3>My Profile</h3>
                    </a>
                    <a href="student_fees.php" class="quick-action-card">
                        <i class="fas fa-dollar-sign"></i>
                        <h3>Pay Fees</h3>
                    </a>
                    <a href="student_attendance.php" class="quick-action-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>Attendance</h3>
                    </a>
                    <a href="student_performance.php" class="quick-action-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Results</h3>
                    </a>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
                    <!-- Left Column -->
                    <div>
                        <!-- Student Information -->
                        <div class="info-card">
                            <h3><i class="fas fa-user"></i> Personal Information</h3>
                            <div class="info-row">
                                <span class="info-label">Full Name</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Course</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['course'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Status</span>
                                <span class="info-value status-<?php echo strtolower($student['status']); ?>">
                                    <i class="fas fa-circle"></i> <?php echo $student['status']; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Fees Summary -->
                        <div class="info-card">
                            <h3><i class="fas fa-dollar-sign"></i> Fees Summary</h3>
                            <div class="info-row">
                                <span class="info-label">Total Fees</span>
                                <span class="info-value">$<?php echo number_format($student['fees_total'], 2); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Amount Paid</span>
                                <span class="info-value" style="color: #28a745;">$<?php echo number_format($student['fees_paid'], 2); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pending Balance</span>
                                <span class="info-value" style="color: <?php echo $student['fees_pending'] > 0 ? '#dc3545' : '#28a745'; ?>;">
                                    $<?php echo number_format($student['fees_pending'], 2); ?>
                                </span>
                            </div>
                            <?php if ($student['fees_pending'] > 0): ?>
                            <div style="margin-top: 15px;">
                                <a href="student_fees.php" class="btn btn-success" style="width: 100%; justify-content: center;">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <!-- Notice Board -->
                        <div class="info-card">
                            <h3><i class="fas fa-bullhorn"></i> Notice Board</h3>
                            <?php if (count($notices) > 0): ?>
                                <?php foreach ($notices as $notice): ?>
                                <div class="notice-item">
                                    <h4><?php echo htmlspecialchars($notice['title']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($notice['content'], 0, 100)) . (strlen($notice['content']) > 100 ? '...' : ''); ?></p>
                                    <div class="notice-date">
                                        <i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($notice['created_at'])); ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="text-align: center; color: #666; padding: 20px;">No notices available.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Academic Info -->
                        <div class="info-card">
                            <h3><i class="fas fa-graduation-cap"></i> Academic Info</h3>
                            <div class="info-row">
                                <span class="info-label">Student ID</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['student_id']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Enrollment Date</span>
                                <span class="info-value"><?php echo $student['enrollment_date'] ? date('M d, Y', strtotime($student['enrollment_date'])) : 'N/A'; ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Date of Birth</span>
                                <span class="info-value"><?php echo $student['date_of_birth'] ? date('M d, Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Auto-refresh session every 5 minutes to keep it alive
        setInterval(function() {
            fetch('refresh_session.php')
                .then(response => response.text())
                .then(data => console.log('Session refreshed'))
                .catch(error => console.error('Error refreshing session:', error));
        }, 300000); // 5 minutes
    </script>
</body>
</html>
