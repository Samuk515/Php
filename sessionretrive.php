<?php
session_start();
if(isset($_SESSION['Visit'])){
    echo "Welcome".$_SESSION['Visit']++;

} else {
    echo"session expired";
}


?>
