<?php
$host='localhost';
$user='root';
$pass='';
$db='Crud';
$conn=mysqli_connect($host,$user,$pass,$db);
if(!$conn)
    {
        die("Database is not connected");
    }
?>