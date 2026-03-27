<?php 
include "connect.php";
session_start();
$profile=$_SESSION['login'];
if($profile==true){

}else{
    header ("Location:http://localhost/webalizer/session/login.php");
}
$sql="select * from login where Username='$profile'";
$result=mysqli_query($conn,$sql);
$num=mysqli_num_rows($result);
echo "welcome".$profile;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <Style>
        img{
            width: 100px;
            height: 100px;
            border-radius: 100px;
        }
    </Style>
</head>
<body>
    <img src="https://via.placeholder.com/100x100?text=Bill+Gates" alt="Bill Gates"> <br>
    <button><a href="logout.php">Logout</a></button>
</body>
</html>