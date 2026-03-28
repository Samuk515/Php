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
    <title>Attendance - Harvard University SIS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .attendance-header {
            background: linear-gradient(135deg, #3E2E23 0%, #5A4233 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .attendance-header h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .attendance-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .attendance-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .attendance-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .attendance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .attendance-card h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .attendance-card p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .attendance-card.total h3 {
            color: #333;
        }
        
        .attendance-card.present h3 {
            color: #28a745;
        }
        
        .attendance-card.absent h3 {
            color: #dc3545;
        }
        
        .attendance-card.percentage h3 {
            color: #3E2E23;
        }
        
        .attendance-calendar {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .attendance-calendar h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .calendar-header h4 {
            color: #333;
            margin: 0;
            font-size: 16px;
        }
        
        .calendar-nav {
            display: flex;
            gap: 10px;
        }
        
        .calendar-nav button {
            background: #f0f0f0;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .calendar-nav button:hover {
            background: #3E2E23;
            color: white;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        .calendar-day-header {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            color: #333;
            font-size: 12px;
        }
        
        .calendar-day {
            background: #f9f9f9;
            padding: 15px 10px;
            text-align: center;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
            position: relative;
        }
        
        .calendar-day.empty {
            background: transparent;
        }
        
        .calendar-day.present {
            background: #d4edda;
            color: #155724;
        }
        
        .calendar-day.absent {
            background: #f8d7da;
            color: #721c24;
        }
        
        .calendar-day.holiday {
            background: #fff3cd;
            color: #856404;
        }
        
        .calendar-day.today {
            border: 2px solid #3E2E23;
        }
        
        .attendance-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #666;
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
        }
        
        .legend-color.present {
            background: #d4edda;
        }
        
        .legend-color.absent {
            background: #f8d7da;
        }
        
        .legend-color.holiday {
            background: #fff3cd;
        }
        
        .attendance-records {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .attendance-records h3 {
            color: #3E2E23;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            font-size: 18px;
        }
        
        .record-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .record-item:last-child {
            border-bottom: none;
        }
        
        .record-date {
            font-weight: bold;
            color: #333;
        }
        
        .record-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .record-status.present {
            background: #d4edda;
            color: #155724;
        }
        
        .record-status.absent {
            background: #f8d7da;
            color: #721c24;
        }
        
        .record-status.holiday {
            background: #fff3cd;
            color: #856404;
        }
        
        @media (max-width: 768px) {
            .attendance-summary {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .calendar-grid {
                grid-template-columns: repeat(7, 1fr);
                font-size: 10px;
            }
            
            .calendar-day {
                padding: 10px 5px;
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
                <li class="active"><a href="student_attendance.php" aria-current="page"><i class="fas fa-calendar-check"></i> Attendance</a></li>
                <li><a href="student_fees.php"><i class="fas fa-dollar-sign"></i> Fees</a></li>
                <li><a href="student_performance.php"><i class="fas fa-chart-line"></i> Performance</a></li>
                <li><a href="student_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <!-- Topbar -->
            <header class="topbar">
                <h2><i class="fas fa-calendar-check"></i> Attendance</h2>
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

                <!-- Attendance Header -->
                <div class="attendance-header">
                    <h2><i class="fas fa-calendar-check"></i> Attendance Tracker</h2>
                    <p>Monitor your attendance record and stay on track</p>
                </div>

                <!-- Attendance Summary -->
                <div class="attendance-summary">
                    <div class="attendance-card total">
                        <h3>22</h3>
                        <p>Total Days</p>
                    </div>
                    <div class="attendance-card present">
                        <h3>20</h3>
                        <p>Present</p>
                    </div>
                    <div class="attendance-card absent">
                        <h3>2</h3>
                        <p>Absent</p>
                    </div>
                    <div class="attendance-card percentage">
                        <h3>91%</h3>
                        <p>Attendance Rate</p>
                    </div>
                </div>

                <!-- Attendance Calendar -->
                <div class="attendance-calendar">
                    <h3><i class="fas fa-calendar"></i> March 2024</h3>
                    <div class="calendar-header">
                        <h4>Monthly Overview</h4>
                        <div class="calendar-nav">
                            <button><i class="fas fa-chevron-left"></i></button>
                            <button><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="calendar-grid">
                        <div class="calendar-day-header">Sun</div>
                        <div class="calendar-day-header">Mon</div>
                        <div class="calendar-day-header">Tue</div>
                        <div class="calendar-day-header">Wed</div>
                        <div class="calendar-day-header">Thu</div>
                        <div class="calendar-day-header">Fri</div>
                        <div class="calendar-day-header">Sat</div>
                        
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day present">1</div>
                        <div class="calendar-day holiday">2</div>
                        
                        <div class="calendar-day holiday">3</div>
                        <div class="calendar-day present">4</div>
                        <div class="calendar-day present">5</div>
                        <div class="calendar-day present">6</div>
                        <div class="calendar-day present">7</div>
                        <div class="calendar-day present">8</div>
                        <div class="calendar-day holiday">9</div>
                        
                        <div class="calendar-day holiday">10</div>
                        <div class="calendar-day present">11</div>
                        <div class="calendar-day present">12</div>
                        <div class="calendar-day present">13</div>
                        <div class="calendar-day present">14</div>
                        <div class="calendar-day present">15</div>
                        <div class="calendar-day holiday">16</div>
                        
                        <div class="calendar-day holiday">17</div>
                        <div class="calendar-day present">18</div>
                        <div class="calendar-day present">19</div>
                        <div class="calendar-day present">20</div>
                        <div class="calendar-day present">21</div>
                        <div class="calendar-day present">22</div>
                        <div class="calendar-day holiday">23</div>
                        
                        <div class="calendar-day holiday">24</div>
                        <div class="calendar-day present">25</div>
                        <div class="calendar-day present">26</div>
                        <div class="calendar-day present">27</div>
                        <div class="calendar-day today">28</div>
                        <div class="calendar-day">29</div>
                        <div class="calendar-day">30</div>
                        
                        <div class="calendar-day">31</div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                        <div class="calendar-day empty"></div>
                    </div>
                    <div class="attendance-legend">
                        <div class="legend-item">
                            <div class="legend-color present"></div>
                            <span>Present</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color absent"></div>
                            <span>Absent</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color holiday"></div>
                            <span>Holiday</span>
                        </div>
                    </div>
                </div>

                <!-- Attendance Records -->
                <div class="attendance-records">
                    <h3><i class="fas fa-list"></i> Recent Attendance Records</h3>
                    <div class="record-item">
                        <span class="record-date">March 28, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 27, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 26, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 25, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 22, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 21, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 20, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 19, 2024</span>
                        <span class="record-status absent">Absent</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 18, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                    <div class="record-item">
                        <span class="record-date">March 15, 2024</span>
                        <span class="record-status present">Present</span>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
