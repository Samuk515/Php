<!DOCTYPE html>
<html>
<head>
<title>Login</title>
</head>
<body>

<form method="POST">
Username:
<input type="text" name="username"><br><br>

Password:
<input type="password" name="password"><br><br>

Role:
<select name="role">
<option value="admin">Admin</option>
<option value="user">User</option>
</select><br><br>

<input type="submit" value="Login">
</form>

<?php
include 'connect.php';

if(isset($_POST['username']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='$role'";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0)
    {
        echo "Login Successful";
    }
    else
    {
        echo "Login Failed";
    }
}
?>

</body>
</html>