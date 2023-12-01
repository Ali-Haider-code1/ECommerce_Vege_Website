<?php
session_start();
require_once("db.php");

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = mysqli_real_escape_string($con, $_POST['itemName']);

    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $selectUserIdQuery = "SELECT id FROM user WHERE email = '$email'";
        $result = mysqli_query($con, $selectUserIdQuery);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                $userId = $row['id'];
                $selectCartItemQuery = "SELECT id, quantity FROM cart WHERE user_id = $userId AND productname = '$itemName'";
                $cartItemResult = mysqli_query($con, $selectCartItemQuery);

                if ($cartItemResult && mysqli_num_rows($cartItemResult) > 0) {
                    $cartItem = mysqli_fetch_assoc($cartItemResult);

                    if ($cartItem['quantity'] > 1) {
                        $updateQuantityQuery = "UPDATE cart SET quantity = quantity - 1 WHERE id = {$cartItem['id']}";
                        if (mysqli_query($con, $updateQuantityQuery)) {
                            $response['success'] = true;
                            $response['message'] = 'Item quantity decreased successfully.';
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Error decreasing item quantity: ' . mysqli_error($con);
                        }
                    } else {
                        $removeQuery = "DELETE FROM cart WHERE id = {$cartItem['id']}";
                        if (mysqli_query($con, $removeQuery)) {
                            $response['success'] = true;
                            $response['message'] = 'Item removed successfully.';
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Error removing item: ' . mysqli_error($con);
                        }
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Item not found in the cart.';
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'User not found.';
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Error: ' . $selectUserIdQuery . '<br>' . mysqli_error($con);
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'User is not logged in.';
    }

    echo json_encode($response);
}
