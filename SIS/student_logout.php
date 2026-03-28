<?php
session_start();
session_destroy();
header("Location: student_login.php?msg=logged_out");
exit();
?>
