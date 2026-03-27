<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        img{
            width:100px ;
        }
        a{
            text-decoration:none;
        }
table{
    border-collapse:collapse;
}
    </style>
</head>
<body>
    <table border='1' cellspacing='5' cellpadding='5'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Age</th>
            <th>Address</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Mobile</th>
            <th>Photo</th>
            <th>CV</th>
            <th colspan='2'>Action</th>
        </tr>
    
    <?php
   include 'Connect.php';
   $sql= "select * from person";
   $result= mysqli_query($conn,$sql);
   $num=mysqli_num_rows($result);
    if($num>0){
        while($row=mysqli_fetch_assoc($result))
            {
            echo "<tr>";
            echo "<td>".$row['Id']."</td>";
            echo "<td>".$row['Name']."</td>";
            echo "<td>".$row['Email']."</td>";
            echo "<td>".$row['Age']."</td>";
            echo "<td>".$row['Address']."</td>";
            echo "<td>".$row['DOB']."</td>";
            echo "<td>".$row['Gender']."</td>";
            echo "<td>".$row['Mobile']."</td>";
            ?>
           <td> <img src="<?php echo $row['Photo'];?>" alt=""></td>
        <td> <a href="<?php echo $row['CV'];?>">View CV</a></td>
        <td><button><a href="update.php?Id=<?php echo $row['Id'];?>">UPDATE</a></button></td>
    <td><button><a href="delete.php">DELETE</a></button>
        </td>
        
            <?php
            
            echo "</tr>";

            }
    }
    ?>
    </table>
    <button><a href="Form.php">Add new record</a></button>
</body>
</html>