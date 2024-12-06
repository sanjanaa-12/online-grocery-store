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

$user_id = $_SESSION['user_id'];

// Initialize the cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if the product is already in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Update quantity if already in the cart
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        // Add new product to the cart
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Redirect to cart page after adding to cart
    header('Location: cart.php');
    exit();
}

// Handle updating item quantity
if (isset($_POST['update_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Update the quantity of the item in the cart
    if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        // Remove the item from the cart if quantity is 0 or less
        unset($_SESSION['cart'][$product_id]);
    }

    // Redirect to cart page after updating
    header('Location: cart.php');
    exit();
}

// Handle removing an item from the cart
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];

    // Remove the item from the cart
    unset($_SESSION['cart'][$product_id]);

    // Redirect to cart page after removing
    header('Location: cart.php');
    exit();
}

// Get the cart items and total value
$cart_total = 0;
$cart_items = [];
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        $cart_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'total' => $product['price'] * $quantity
        ];
        $cart_total += $product['price'] * $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #555;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .total {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
        .proceed-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .proceed-btn:hover {
            background-color: #218838;
        }
        .empty-cart {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Your Cart</h2>
        
        <?php if (count($cart_items) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="update_cart">Update</button>
                            </form>
                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['total'], 2); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove_item" style="color: red;">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total">
                Total: $<?php echo number_format($cart_total, 2); ?>
            </div>

            <form method="POST" action="checkout.php">
                <button type="submit" class="proceed-btn">Proceed to Checkout</button>
            </form>
        <?php else: ?>
            <p class="empty-cart">Your cart is empty.</p>
        <?php endif; ?>
    </div>

</body>
</html>
