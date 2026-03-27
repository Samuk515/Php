<?php
include 'conn.php';
$id=$_GET['id'];
$sql="select * from person where id='$id'";
$result=mysqli_query($conn,$sql);
$row=mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        a{
            text-decoration: none;
            color: black;
        }
        </style>
</head>
<body>
    <h2>Form</h2>
    <form action="" method="Post" enctype="multipart/form-data">
        Name:
        <input type="text" name="Name" id="" value=<?php echo $row['Name'];?>> <br>

        Email:
        <input type="email" name="Email" id="" value=<?php echo $row['Email'];?>><br>

        Age:
        <input type="text" name="Age" id="" value=<?php echo $row['Age'];?>><br>

        Address:
        <select name="Address" id="">
            <option value="Kathmandu"  <?php if($row['Address']=="Kathmandu") echo "selected"?>>Kathmandu</option>
            <option value="Pokhara" <?php if($row['Address']=="Pokhara") echo "selected"?>>Pokhara</option>
            <option value="Chitwan" <?php if($row['Address']=="Chitwan") echo "selected"?>>Chitwan</option>
            <option value="Jhapa" <?php if($row['Address']=="Jhapa") echo "selected"?>>Jhapa</option>

        Dob:
        <input type="date" name="Date"><br>

        Gender:
        <input type="radio" name="Gender" value="Male"> Male
        <input type="radio" name="Gender" value="Male"> Female
        <br>

        Mobile:
        <input type="num" name="Mobile" id="" value=<?php echo $row['Mobile'];?>> <br>

        photo:
        <input type="file" name="Photo" accept="image/*"><br>

        C.V :
        <input type="file" name="CV" accept="application/pdf"><br>

        <input type="Submit" name="Submit">
        <button><a href="view.php">view records</a></button>

</form>
<?php
include 'conn.php';
if(isset($_POST['Submit']))
    {
        $Name=$_POST['Name'];
        $Email=$_POST['Email'];
        $Age=$_POST['Age'];
        $Address=$_POST['Address'];
        $DOB=$_POST['Date'];
        $Gender=$_POST['Gender'];
        $Mobile=$_POST['Mobile'];
        $pic=$_FILES['Photo']['name'];
        $temp1=$_FILES['Photo']['tmp_name'];
        $folder1='Pic/'.$pic;
        move_uploaded_file(from:$temp1,to:$folder1);
        $cv = $_FILES['CV']['name'];
        $temp2 = $_FILES['CV']['tmp_name'];
        $folder2 = 'CV/' . $cv;
        move_uploaded_file(from: $temp2, to: $folder2);
        $sql="insert into person (Name,Email,Age,Address,Dob,Gender,Mobile,Photo,Cv)
        values('$Name','$Email','$Age','$Address','$DOB','$Gender','$Mobile','$folder1','$folder2')";
        $result=mysqli_query($conn,$sql);
        if($result)
            {
                echo"records were inserted sucessfully";
            }
            else{
                echo"not inserted";
            }

    }
?>
</body>
</html>