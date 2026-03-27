<?php
$host='localhost';
$user='root';
$pass='';
$db='Pathao';
$conn=mysqli_connect($host,$user,$pass,$db);
if(!$conn)
    {
        die("Database is not connected");
    }
?>