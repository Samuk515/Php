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
    <title>Performance - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .performance-header {
            background: linear-gradient(135deg, #3E2E23 0%, #5A4233 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .performance-header h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .performance-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .performance-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .performance-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .performance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .performance-card h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .performance-card p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .performance-card.gpa h3 {
            color: #3E2E23;
        }
        
        .performance-card.rank h3 {
            color: #28a745;
        }
        
        .performance-card.courses h3 {
            color: #17a2b8;
        }
        
        .performance-card.credits h3 {
            color: #ffc107;
        }
        
        .gpa-chart {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .gpa-chart h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .chart-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 200px;
            padding: 20px 0;
        }
        
        .chart-bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 60px;
        }
        
        .bar {
            width: 40px;
            background: linear-gradient(180deg, #3E2E23 0%, #5A4233 100%);
            border-radius: 5px 5px 0 0;
            transition: height 0.5s ease;
        }
        
        .bar-label {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        .bar-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .courses-list {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .courses-list h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .course-item:last-child {
            border-bottom: none;
        }
        
        .course-info h4 {
            color: #333;
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        
        .course-info p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        .course-grade {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .course-grade.a {
            background: #d4edda;
            color: #155724;
        }
        
        .course-grade.b {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .course-grade.c {
            background: #fff3cd;
            color: #856404;
        }
        
        .course-grade.d {
            background: #f8d7da;
            color: #721c24;
        }
        
        .semester-selector {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .semester-selector h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .semester-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .semester-option {
            padding: 12px 24px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            color: #333;
        }
        
        .semester-option:hover {
            border-color: #3E2E23;
            background: #f9f9f9;
        }
        
        .semester-option.active {
            background: #3E2E23;
            color: white;
            border-color: #3E2E23;
        }
        
        .achievements {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .achievements h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .achievement-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .achievement-item:last-child {
            border-bottom: none;
        }
        
        .achievement-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3E2E23 0%, #5A4233 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .achievement-info h4 {
            color: #333;
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        
        .achievement-info p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .performance-summary {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .chart-container {
                flex-wrap: wrap;
                height: auto;
            }
            
            .chart-bar {
                margin-bottom: 20px;
            }
            
            .semester-options {
                flex-direction: column;
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
                <li><a href="student_fees.php"><i class="fas fa-dollar-sign"></i> Fees</a></li>
                <li class="active"><a href="student_performance.php" aria-current="page"><i class="fas fa-chart-line"></i> Performance</a></li>
                <li><a href="student_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <!-- Topbar -->
            <header class="topbar">
                <h2><i class="fas fa-chart-line"></i> Performance</h2>
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

                <!-- Performance Header -->
                <div class="performance-header">
                    <h2><i class="fas fa-chart-line"></i> Academic Performance</h2>
                    <p>Track your grades, GPA, and academic achievements</p>
                </div>

                <!-- Performance Summary -->
                <div class="performance-summary">
                    <div class="performance-card gpa">
                        <h3>3.75</h3>
                        <p>Current GPA</p>
                    </div>
                    <div class="performance-card rank">
                        <h3>5th</h3>
                        <p>Class Rank</p>
                    </div>
                    <div class="performance-card courses">
                        <h3>6</h3>
                        <p>Enrolled Courses</p>
                    </div>
                    <div class="performance-card credits">
                        <h3>18</h3>
                        <p>Total Credits</p>
                    </div>
                </div>

                <!-- GPA Chart -->
                <div class="gpa-chart">
                    <h3><i class="fas fa-chart-bar"></i> GPA Trend</h3>
                    <div class="chart-container">
                        <div class="chart-bar">
                            <span class="bar-value">3.5</span>
                            <div class="bar" style="height: 140px;"></div>
                            <span class="bar-label">Sem 1</span>
                        </div>
                        <div class="chart-bar">
                            <span class="bar-value">3.6</span>
                            <div class="bar" style="height: 144px;"></div>
                            <span class="bar-label">Sem 2</span>
                        </div>
                        <div class="chart-bar">
                            <span class="bar-value">3.7</span>
                            <div class="bar" style="height: 148px;"></div>
                            <span class="bar-label">Sem 3</span>
                        </div>
                        <div class="chart-bar">
                            <span class="bar-value">3.65</span>
                            <div class="bar" style="height: 146px;"></div>
                            <span class="bar-label">Sem 4</span>
                        </div>
                        <div class="chart-bar">
                            <span class="bar-value">3.75</span>
                            <div class="bar" style="height: 150px;"></div>
                            <span class="bar-label">Sem 5</span>
                        </div>
                    </div>
                </div>

                <!-- Semester Selector -->
                <div class="semester-selector">
                    <h3><i class="fas fa-calendar-alt"></i> Select Semester</h3>
                    <div class="semester-options">
                        <div class="semester-option">Fall 2023</div>
                        <div class="semester-option">Spring 2024</div>
                        <div class="semester-option active">Current Semester</div>
                        <div class="semester-option">Summer 2024</div>
                    </div>
                </div>

                <!-- Courses List -->
                <div class="courses-list">
                    <h3><i class="fas fa-book"></i> Current Courses</h3>
                    <div class="course-item">
                        <div class="course-info">
                            <h4>Computer Science 101</h4>
                            <p>Introduction to Programming</p>
                        </div>
                        <span class="course-grade a">A</span>
                    </div>
                    <div class="course-item">
                        <div class="course-info">
                            <h4>Mathematics 201</h4>
                            <p>Calculus II</p>
                        </div>
                        <span class="course-grade a">A-</span>
                    </div>
                    <div class="course-item">
                        <div class="course-info">
                            <h4>Physics 101</h4>
                            <p>General Physics</p>
                        </div>
                        <span class="course-grade b">B+</span>
                    </div>
                    <div class="course-item">
                        <div class="course-info">
                            <h4>English 102</h4>
                            <p>Academic Writing</p>
                        </div>
                        <span class="course-grade a">A</span>
                    </div>
                    <div class="course-item">
                        <div class="course-info">
                            <h4>History 101</h4>
                            <p>World History</p>
                        </div>
                        <span class="course-grade b">B</span>
                    </div>
                    <div class="course-item">
                        <div class="course-info">
                            <h4>Economics 101</h4>
                            <p>Microeconomics</p>
                        </div>
                        <span class="course-grade a">A-</span>
                    </div>
                </div>

                <!-- Achievements -->
                <div class="achievements">
                    <h3><i class="fas fa-trophy"></i> Achievements</h3>
                    <div class="achievement-item">
                        <div class="achievement-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="achievement-info">
                            <h4>Dean's List</h4>
                            <p>Achieved Dean's List for Spring 2024 semester</p>
                        </div>
                    </div>
                    <div class="achievement-item">
                        <div class="achievement-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div class="achievement-info">
                            <h4>Perfect Attendance</h4>
                            <p>100% attendance for Fall 2023 semester</p>
                        </div>
                    </div>
                    <div class="achievement-item">
                        <div class="achievement-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="achievement-info">
                            <h4>Academic Excellence</h4>
                            <p>Top 10% in Computer Science department</p>
                        </div>
                    </div>
                    <div class="achievement-item">
                        <div class="achievement-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="achievement-info">
                            <h4>Python Certification</h4>
                            <p>Completed Python programming certification</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
