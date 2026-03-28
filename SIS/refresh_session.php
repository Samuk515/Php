<?php
session_start();
if (isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = time();
    echo 'Session refreshed';
} else {
    echo 'No active session';
}
?>
