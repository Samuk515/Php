<?php
require_once 'config.php';
requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit();
}

$id = (int)$_GET['id'];

// Fetch student data
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

// Delete student
$delete_query = "DELETE FROM students WHERE id = ?";
$delete_stmt = mysqli_prepare($conn, $delete_query);
mysqli_stmt_bind_param($delete_stmt, "i", $id);

if (mysqli_stmt_execute($delete_stmt)) {
    setFlashMessage('success', "Student '{$student['first_name']} {$student['last_name']}' ({$student['student_id']}) has been deleted successfully.");
} else {
    setFlashMessage('danger', 'Error deleting student: ' . mysqli_error($conn));
}

header("Location: students.php");
exit();
?>
