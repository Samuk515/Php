<?php
session_start();
if(isset($_SESSION['Name'])){
    $_SESSION['Name']='Samir';
    echo "Welcome".$_SESSION['Name'];

} else {
    echo"session expired";
}


?>