<?php
include "connect.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        Username: <input type="text" name="Username" id=""> <br>
        Password: <input type="password" name="Password" id=""> <br>
        <input type="submit" name="submit" id="">
    </form>
    <?php 
    if(isset($_POST['submit'])){
        $User=$_POST['Username'];
        $Pass=$_POST['Password'];
        $sql="select * from login where Username='$User' and Password='$Pass'";
        $result=mysqli_query($conn,$sql);
        $num=mysqli_num_rows($result);
        if($num>=1){
            $_SESSION['login']=$User;
            header("Location:http://localhost/webalizer/session/welcome.php");
        } else {
            echo "error in login";
        }
    }
    ?>
</body>
</html>