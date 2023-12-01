<?php
session_start();
require_once("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $selectUserIdQuery = "SELECT id FROM user WHERE email = '$email'";
        $result = mysqli_query($con, $selectUserIdQuery);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                $userId = $row['id'];
                $productName = $_POST['productName'];
                $productPrice = $_POST['productPrice'];

                $insertQuery = "INSERT INTO cart (user_id, productname, price) VALUES ('$userId', '$productName', '$productPrice')";

                if (mysqli_query($con, $insertQuery)) {
                    echo '<script>window.location.href = "cart.php";</script>';
                    echo 'Item added to cart successfully.';
                } else {
                    echo 'Error: ' . $insertQuery . '<br>' . mysqli_error($con);
                }
            } else {
                echo 'User not found.';
            }
        } else {
            echo 'Error: ' . $selectUserIdQuery . '<br>' . mysqli_error($con);
        }
    } else {
        echo 'User is not logged in.';
    }
}
