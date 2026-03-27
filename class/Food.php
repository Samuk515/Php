<!DOCTYPE html>
<html lang="en">
<head>
   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Managment System </title>
    <style>
        .status.pending {
            color: green;
        }
        .status.preparing {
            color: yellow;
        }
        .status.delivered {
            color: red;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <h1>Food Managment System </h1>
        Customer Name: <input type="text" name="customer_name" required><br>
         Food item: <input type="text" name="food_item" required><br>
         Quantity: <input type="number" name="quantity" required><br>
         Status: <select name="status" required>
            <option value="pending">Pending</option>
            <option value="preparing">Preparing</option>
            <option value="delivered">Delivered</option>
         </select><br>
         <input type="submit" value="Submit">    
    </form>
    <?php
    include 'connect.php';

    // Create orders table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        food_item VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        status ENUM('pending', 'preparing', 'delivered') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $createTable);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $customer_name = $_POST['customer_name'];
        $food_item = $_POST['food_item'];
        $quantity = $_POST['quantity'];
        $status = $_POST['status'];
        
        $sql = "INSERT INTO orders (customer_name, food_item, quantity, status) VALUES ('$customer_name', '$food_item', '$quantity', '$status')";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            echo "Data inserted successfully<br>";
        } else {
            echo "Data insertion failed: " . mysqli_error($conn) . "<br>";
        }   
        
        echo "<h2>Order Details:</h2>";
        echo "Customer Name: $customer_name<br>";
        echo "Food Item: $food_item<br>";
        echo "Quantity: $quantity<br>";
        echo "Status: <span class='status $status'>" . ucfirst($status) . "</span><br>";
    }
    ?>
</body>
</html>