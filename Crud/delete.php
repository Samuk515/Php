<?php
include 'conn.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "delete from avash where id='$id'";
    $res = mysqli_query($conn, $sql);
    if($res){
        ?>
    <script>
        confirm("really want to delete");
    </script>
        <?php
        echo "Record were deleted successifylly";
        header("Location:http://localhost/crudPhp/view.php");
    }else{
        echo "something went wrong";
    }
}
?>