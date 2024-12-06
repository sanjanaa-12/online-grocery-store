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

// Add product to wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_wishlist'])) {
    $product_id = $_POST['product_id'];

    // Add product to wishlist if not already there
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) 
                            SELECT ?, ? FROM dual 
                            WHERE NOT EXISTS (SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?)");
    $stmt->bind_param("iiii", $user_id, $product_id, $user_id, $product_id);
    $stmt->execute();
}

// Delete product from wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_from_wishlist'])) {
    $product_id = $_POST['product_id'];

    // Remove the product from the wishlist
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

// Fetch the wishlist items for the user
$stmt = $conn->prepare("SELECT p.* FROM products p 
                        JOIN wishlist w ON p.id = w.product_id 
                        WHERE w.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            justify-items: center;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 250px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .product-card .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            padding: 10px;
        }

        .product-card .product-price {
            color: #007bff;
            font-size: 16px;
            padding: 10px;
        }

        .product-card button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 0 0 8px 8px;
            transition: background-color 0.3s ease;
        }

        .product-card button:hover {
            background-color: #c0392b;
        }

        .empty-message {
            text-align: center;
            font-size: 18px;
            color: #555;
            padding: 50px;
        }
    </style>
</head>
<body>
    <header>
    <h1>Welcome <?php echo htmlspecialchars($username); ?> to Your Wishlist</h1>
    </header>

    <h2>Your Wishlist</h2>

    <div class="product-list">
        <?php if ($wishlist_result->num_rows > 0): ?>
            <?php while ($row = $wishlist_result->fetch_assoc()): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                <div class="product-name"><?php echo htmlspecialchars($row['name']); ?></div>
                <div class="product-price">$<?php echo number_format($row['price'], 2); ?></div>

                <!-- Delete button -->
                <form method="POST" action="wishlist.php" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_from_wishlist">Remove from Wishlist</button>
                </form>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-message">Your wishlist is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
