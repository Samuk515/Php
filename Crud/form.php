<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        a{
            text-decoration:none;
        }
    </style>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
        Name:<input type="text" name="name"><br>
        Email: <input type="email" name="email" id=""><br>
        Age: <input type="number" name="age" id=""><br>
        Address: <select name="address" id="">
            <option value="Kathmandu">Kathmandu</option>
            <option value="Pokhara">Pokhara</option>
            <option value="Chitwan">Chitwan</option>
            <option value="Jhapa">Jhapa</option>
        </select><br>
        DOB: <input type="date" name="dob" id=""><br>
        Gender: <input type="radio" name="gender" id="" value="Male">Male
        <input type="radio" name="gender" id="" value="Female">Female
        <input type="radio" name="gender" id="" value="Other">Other 
        <br>
        Mobile: <input type="tel" name="mobile" id=""><br>
        Photo
        <input type="file" name="Photo" id="" accept="image/*"><br>
        CV:
        <input type="file" name="CV" id="" accept="application/*"><br>
        <input type="submit" name="Submit" id="" value="submit">
        <button><a href="view.php">view records</a></button>
    </form>
    <?php
    include 'connect.php';
    if(isset($_POST['Submit'])){
        $Name=$_POST['name'];
        $Email=$_POST['email'];
        $Age=$_POST['age'];
        $Address=$_POST['address'];
        $DOB=$_POST['dob'];
        $Gender=$_POST['gender'];
        $Mobile=$_POST['mobile'];
        $pic=$_FILES['Photo']['name'];
        $temp1=$_FILES['Photo']['tmp_name'];
        $folder1="Pic/".$pic;
        move_uploaded_file($temp1,$folder1);
        $cv=$_FILES['CV']['name'];
        $temp2=$_FILES['CV']['tmp_name'];
        $folder2="Cv/".$cv;
        move_uploaded_file($temp2,$folder2);
        $sql="insert into person (Name,Email,Age,Address,Dob,Gender,Mobile,Photo,CV)
        values('$Name','$Email','$Age','$Address','$DOB','$Gender','$Mobile','$folder1','$folder2')";
        $result=mysqli_query($conn,$sql);
        if($result)
        {
            echo "records were inserted successfully";
        }
        else{
            echo "Records were not inserted successfully";
        }
    }
    ?>
</body>
</html>