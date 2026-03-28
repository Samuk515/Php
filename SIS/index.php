<?php
require_once 'config.php';
requireAdmin();

// Get statistics
$stats = getDashboardStats($conn);
$notices = getNotices($conn, 5);

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Harvard University SIS</title>
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
                <li class="active"><a href="index.php" aria-current="page"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="students.php"><i class="fas fa-users"></i> Students</a></li>
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
                    <a class="btn" href="add_student.php"><i class="fas fa-user-plus"></i> Add New Student</a>
                    <a class="btn btn-secondary" href="logout.php" style="margin-left: 10px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </header>

            <!-- Dashboard Main -->
            <section class="dashboard" role="main">
                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo $flash['message']; ?>
                    </div>
                <?php endif; ?>

                <div class="header">
                    <h1>Dashboard</h1>
                    <p>Home / Dashboard</p>
                </div>

                <!-- Stats Grid -->
                <div class="detail-box">
                    <div class="detail">
                        <h3><?php echo number_format($stats['total_students']); ?></h3>
                        <p>Total Students</p>
                    </div>
                    <div class="detail">
                        <h3><?php echo number_format($stats['total_teachers']); ?></h3>
                        <p>Teachers</p>
                    </div>
                    <div class="detail">
                        <h3>$<?php echo number_format($stats['pending_fees'], 2); ?></h3>
                        <p>Pending Dues</p>
                    </div>
                    <div class="detail">
                        <h3>$<?php echo number_format($stats['collected_fees'], 2); ?></h3>
                        <p>Fees Collected</p>
                    </div>
                </div>

                <!-- Notice Board -->
                <div class="table-box">
                    <h2><i class="fas fa-bullhorn"></i> Notice Board</h2>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S.N.</th>
                                    <th>Notice</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($notices) > 0): ?>
                                    <?php foreach ($notices as $index => $notice): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($notice['title']); ?></strong><br>
                                                <small style="color: #666;"><?php echo htmlspecialchars(substr($notice['content'], 0, 100)) . '...'; ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($notice['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="empty-state">No notices available.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="detail-box" style="grid-template-columns: repeat(2, 1fr);">
                    <div class="detail">
                        <h3><?php echo number_format($stats['active_students']); ?></h3>
                        <p>Active Students</p>
                    </div>
                    <div class="detail">
                        <h3><?php echo number_format($stats['total_courses']); ?></h3>
                        <p>Total Courses</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
