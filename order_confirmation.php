<?php
ini_set('session.cookie_lifetime', 0); 
ini_set('session.gc_maxlifetime', 0);
session_start();
require 'db.php';
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure the order is placed
if (!isset($_SESSION['order_id'])) {
    header('Location: payment.php');
    exit();
}

$order_id = $_SESSION['order_id'];
$order_date = $_SESSION['order_date'];
$address = $_SESSION['address'];
$payment_method = $_SESSION['payment_method'];
// Clear the cart session after placing the order
unset($_SESSION['cart']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .order-details {
            margin: 20px 0;
        }
        .order-details p {
            font-size: 1.2em;
        }
        .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
        }
        .button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Order Placed Successfully</h2>
        <div class="order-details">
            <p><strong>Order ID:</strong> <?php echo $order_id; ?></p>
            <p><strong>Order Date:</strong> <?php echo $order_date; ?></p>
            <p><strong>Shipping Address:</strong> <?php echo $address; ?></p>
            <p><strong>Payment Method:</strong> <?php echo ucfirst($payment_method); ?></p>
        </div>
        <button class="button" onclick="window.location.href='index.php'">Back to Home</button>
    </div>

</body>
</html>
