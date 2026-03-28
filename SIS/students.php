<?php
require_once 'config.php';
requireAdmin();

// Search and filter
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : '';
$course_filter = isset($_GET['course']) ? sanitize($conn, $_GET['course']) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build query
$where = "WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $where .= " AND (first_name LIKE ? OR last_name LIKE ? OR student_id LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $types .= 'ssss';
}

if (!empty($status_filter)) {
    $where .= " AND status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($course_filter)) {
    $where .= " AND course = ?";
    $params[] = $course_filter;
    $types .= 's';
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM students $where";
if (!empty($params)) {
    $count_stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    mysqli_stmt_execute($count_stmt);
    $total_result = mysqli_stmt_get_result($count_stmt);
} else {
    $total_result = mysqli_query($conn, $count_query);
}
$total = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total / $per_page);

// Get students
$query = "SELECT * FROM students $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    $all_params = array_merge($params, [$per_page, $offset]);
    mysqli_stmt_bind_param($stmt, $types . "ii", ...$all_params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $per_page, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

// Get unique courses for filter
$courses_result = mysqli_query($conn, "SELECT DISTINCT course FROM students WHERE course IS NOT NULL AND course != '' ORDER BY course");

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Harvard University SIS</title>
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
                    <h1>Students</h1>
                    <p>Home / Students</p>
                </div>

                <!-- Search and Filters -->
                <div class="form-container">
                    <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                            <label for="search">Search</label>
                            <input type="text" id="search" name="search" class="form-control" 
                                   placeholder="Search by name, ID, or email..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="Active" <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo $status_filter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="Graduated" <?php echo $status_filter === 'Graduated' ? 'selected' : ''; ?>>Graduated</option>
                                <option value="Suspended" <?php echo $status_filter === 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                            <label for="course">Course</label>
                            <select id="course" name="course" class="form-control">
                                <option value="">All Courses</option>
                                <?php while ($c = mysqli_fetch_assoc($courses_result)): ?>
                                    <option value="<?php echo htmlspecialchars($c['course']); ?>" 
                                            <?php echo $course_filter === $c['course'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['course']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="students.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </form>
                </div>

                <!-- Students Table -->
                <div class="table-box">
                    <div class="d-flex justify-between align-center" style="margin-bottom: 16px;">
                        <h2><i class="fas fa-list"></i> Student List</h2>
                        <span style="color: #666; font-size: 14px;">
                            Showing <?php echo min($offset + 1, $total); ?>-<?php echo min($offset + $per_page, $total); ?> of <?php echo $total; ?> students
                        </span>
                    </div>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($student = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($student['student_id']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($student['course'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($student['status']); ?>">
                                                    <?php echo $student['status']; ?>
                                                </span>
                                            </td>
                                            <td class="actions">
                                                <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-primary btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-danger btn-sm" title="Delete" 
                                                   onclick="return confirm('Are you sure you want to delete this student?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <div class="empty-icon"><i class="fas fa-search"></i></div>
                                                <h3>No students found</h3>
                                                <p>Try adjusting your search or filter criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&course=<?php echo urlencode($course_filter); ?>">
                                    <i class="fas fa-chevron-left"></i> Prev
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="active"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&course=<?php echo urlencode($course_filter); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&course=<?php echo urlencode($course_filter); ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
