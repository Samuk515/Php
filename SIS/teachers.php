<?php
require_once 'config.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers - Harvard University SIS</title>
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
                <li><a href="students.php"><i class="fas fa-users"></i> Students</a></li>
                <li class="active"><a href="teachers.php" aria-current="page"><i class="fas fa-chalkboard-teacher"></i> Teachers</a></li>
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
                    <h1>Teachers</h1>
                    <p>Home / Teachers</p>
                </div>

                <div class="table-box">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <h3>Teachers Module</h3>
                        <p>This module is under development. Coming soon!</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
