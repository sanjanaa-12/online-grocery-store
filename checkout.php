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

// Get the cart items
$cart_total = 0;
$cart_items = [];
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        $cart_items[] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'total' => $product['price'] * $quantity
        ];
        $cart_total += $product['price'] * $quantity;
    }
}

// Check if the form is submitted
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['next_step'])) {
//     // Store the address in session and redirect to payment page
//     $_SESSION['address'] = [
//         'address1' => $_POST['address1'],
//         'suite' => $_POST['suite'],
//         'street' => $_POST['street'],
//         'city' => $_POST['city'],
//         'state' => $_POST['state'],
//         'zipcode' => $_POST['zipcode']
//     ];
//     header('Location: payment.php');
//     exit();
// }
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['next_step'])) {
    // Concatenate the address fields into a single string
    $address = $_POST['address1'] . ', ' .
               ($_POST['suite'] ? $_POST['suite'] . ', ' : '') . 
               $_POST['street'] . ', ' .
               $_POST['city'] . ', ' .
               $_POST['state'] . ' ' .
               $_POST['zipcode'];

    // Store the concatenated address in session
    $_SESSION['address'] = $address;

    // Redirect to the payment page
    header('Location: payment.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Enter Your Address</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="address1">Address Line 1</label>
                <input type="text" id="address1" name="address1" required>
            </div>
            <div class="form-group">
                <label for="suite">Suite/Apartment (optional)</label>
                <input type="text" id="suite" name="suite">
            </div>
            <div class="form-group">
                <label for="street">Street</label>
                <input type="text" id="street" name="street" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required>
            </div>
            <div class="form-group">
                <label for="zipcode">Zipcode</label>
                <input type="text" id="zipcode" name="zipcode" required>
            </div>

            <button type="submit" name="next_step">Next</button>
        </form>
    </div>

</body>
</html>
