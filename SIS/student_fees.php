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

// Calculate fees percentage
$fees_percentage = $student['fees_total'] > 0 ? round(($student['fees_paid'] / $student['fees_total']) * 100) : 0;

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .fees-header {
            background: linear-gradient(135deg, #3E2E23 0%, #5A4233 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .fees-header h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .fees-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .fees-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .fees-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .fees-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .fees-card h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .fees-card p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .fees-card.total h3 {
            color: #333;
        }
        
        .fees-card.paid h3 {
            color: #28a745;
        }
        
        .fees-card.pending h3 {
            color: <?php echo $student['fees_pending'] > 0 ? '#dc3545' : '#28a745'; ?>;
        }
        
        .progress-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .progress-header h3 {
            color: #333;
            font-size: 18px;
            margin: 0;
        }
        
        .progress-percentage {
            font-size: 24px;
            font-weight: bold;
            color: #3E2E23;
        }
        
        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .fees-details {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .fees-details h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .fee-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .fee-item:last-child {
            border-bottom: none;
        }
        
        .fee-info h4 {
            color: #333;
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        
        .fee-info p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        .fee-amount {
            font-size: 18px;
            font-weight: bold;
        }
        
        .fee-amount.paid {
            color: #28a745;
        }
        
        .fee-amount.pending {
            color: #dc3545;
        }
        
        .payment-actions {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .payment-actions h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .payment-method {
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: #3E2E23;
            background: #f9f9f9;
        }
        
        .payment-method i {
            font-size: 32px;
            color: #3E2E23;
            margin-bottom: 10px;
        }
        
        .payment-method h4 {
            color: #333;
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        
        .payment-method p {
            color: #666;
            margin: 0;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .fees-summary {
                grid-template-columns: 1fr;
            }
            
            .payment-methods {
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
                <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="student_attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
                <li class="active"><a href="student_fees.php" aria-current="page"><i class="fas fa-dollar-sign"></i> Fees</a></li>
                <li><a href="student_performance.php"><i class="fas fa-chart-line"></i> Performance</a></li>
                <li><a href="student_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <!-- Topbar -->
            <header class="topbar">
                <h2><i class="fas fa-dollar-sign"></i> Fees</h2>
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

                <!-- Fees Header -->
                <div class="fees-header">
                    <h2><i class="fas fa-dollar-sign"></i> Fee Payment Portal</h2>
                    <p>Manage your fee payments and view payment history</p>
                </div>

                <!-- Fees Summary -->
                <div class="fees-summary">
                    <div class="fees-card total">
                        <h3>$<?php echo number_format($student['fees_total'], 2); ?></h3>
                        <p>Total Fees</p>
                    </div>
                    <div class="fees-card paid">
                        <h3>$<?php echo number_format($student['fees_paid'], 2); ?></h3>
                        <p>Amount Paid</p>
                    </div>
                    <div class="fees-card pending">
                        <h3>$<?php echo number_format($student['fees_pending'], 2); ?></h3>
                        <p>Pending Balance</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-header">
                        <h3>Payment Progress</h3>
                        <span class="progress-percentage"><?php echo $fees_percentage; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $fees_percentage; ?>%">
                            <?php echo $fees_percentage; ?>%
                        </div>
                    </div>
                    <p style="margin-top: 10px; color: #666; font-size: 14px;">
                        <?php if ($fees_percentage >= 100): ?>
                            <i class="fas fa-check-circle" style="color: #28a745;"></i> Congratulations! Your fees are fully paid.
                        <?php else: ?>
                            <i class="fas fa-info-circle" style="color: #17a2b8;"></i> You have paid <?php echo $fees_percentage; ?>% of your total fees.
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Fee Details -->
                <div class="fees-details">
                    <h3><i class="fas fa-list"></i> Fee Breakdown</h3>
                    <div class="fee-item">
                        <div class="fee-info">
                            <h4>Tuition Fee</h4>
                            <p>Academic tuition for the current semester</p>
                        </div>
                        <span class="fee-amount">$<?php echo number_format($student['fees_total'] * 0.7, 2); ?></span>
                    </div>
                    <div class="fee-item">
                        <div class="fee-info">
                            <h4>Library Fee</h4>
                            <p>Access to library resources and facilities</p>
                        </div>
                        <span class="fee-amount">$<?php echo number_format($student['fees_total'] * 0.1, 2); ?></span>
                    </div>
                    <div class="fee-item">
                        <div class="fee-info">
                            <h4>Lab Fee</h4>
                            <p>Laboratory equipment and materials</p>
                        </div>
                        <span class="fee-amount">$<?php echo number_format($student['fees_total'] * 0.15, 2); ?></span>
                    </div>
                    <div class="fee-item">
                        <div class="fee-info">
                            <h4>Other Fees</h4>
                            <p>Miscellaneous administrative fees</p>
                        </div>
                        <span class="fee-amount">$<?php echo number_format($student['fees_total'] * 0.05, 2); ?></span>
                    </div>
                </div>

                <!-- Payment Actions -->
                <?php if ($student['fees_pending'] > 0): ?>
                <div class="payment-actions">
                    <h3><i class="fas fa-credit-card"></i> Make a Payment</h3>
                    <div class="payment-methods">
                        <div class="payment-method">
                            <i class="fas fa-credit-card"></i>
                            <h4>Credit/Debit Card</h4>
                            <p>Pay securely with your card</p>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-university"></i>
                            <h4>Bank Transfer</h4>
                            <p>Direct bank transfer</p>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-money-bill-wave"></i>
                            <h4>Cash Payment</h4>
                            <p>Pay at the finance office</p>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-mobile-alt"></i>
                            <h4>Mobile Payment</h4>
                            <p>Pay via mobile banking</p>
                        </div>
                    </div>
                    <p style="margin-top: 20px; color: #666; font-size: 14px; text-align: center;">
                        <i class="fas fa-info-circle"></i> For online payments, please visit the finance office or contact administration.
                    </p>
                </div>
                <?php else: ?>
                <div class="payment-actions">
                    <h3><i class="fas fa-check-circle"></i> Payment Complete</h3>
                    <p style="text-align: center; color: #28a745; padding: 20px;">
                        <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 15px;"></i><br>
                        All fees have been paid. Thank you!
                    </p>
                </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
