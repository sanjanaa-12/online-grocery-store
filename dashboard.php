<?php
ini_set('session.cookie_lifetime', 0);
session_start();
require 'db.php';
header("Cache-Control: no-store, no-cache, must-revalidate"); // Prevent caching
header("Pragma: no-cache");
header("Expires: 0");
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
// Fetch user details from the database to get the username
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

$username = $user['username'];  // Store the username in a variable

// Handle adding to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];  // Product ID from form
    $quantity = $_POST['quantity'];  // Quantity from form

    // Check if the product already exists in the user's cart
    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the product already exists, update the quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    } else {
        // If the product doesn't exist, insert it into the cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    }

    $stmt->execute();

    // Refresh the cart in session
    $_SESSION['cart'][$product_id] = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] + $quantity : $quantity;

    // Redirect to prevent form resubmission
    header('Location: dashboard.php');
    exit();
}

// Handle adding to the wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_wishlist'])) {
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];

        // Check if the product already exists in the wishlist
        $stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // If not, insert it into the wishlist
            $insert_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $insert_stmt->bind_param("ii", $user_id, $product_id);
            if ($insert_stmt->execute()) {
                $wishlist_message = "Product added to wishlist!";
            } else {
                $wishlist_message = "Error adding product to wishlist.";
            }
        } else {
            // If already in wishlist, show a message
            $wishlist_message = "Product is already in your wishlist.";
        }
    }

    // Redirect to prevent form resubmission
    header('Location: dashboard.php');
    exit();
}

// Get the cart count and total value
$cart_count = 0;
$cart_total = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    // Fetch product details from the database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        $cart_count += $quantity;
        $cart_total += $product['price'] * $quantity;
    }
}

// Get wishlist count
$wishlist_count_query = $conn->prepare("SELECT COUNT(*) AS count FROM wishlist WHERE user_id = ?");
$wishlist_count_query->bind_param("i", $user_id);
$wishlist_count_query->execute();
$wishlist_result = $wishlist_count_query->get_result()->fetch_assoc();
$wishlist_count = $wishlist_result['count'];

// Search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $search_term = "%" . $search . "%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM products");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        /* Remove white space above */
body, html {
    margin: 0;
    padding: 0;
}

nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #28a745;  /* Green navbar */
    padding: 10px 20px;
    color: white;
}

nav a {
    color: white;
    text-decoration: none;
    margin: 0 10px;
}

nav a:hover {
    text-decoration: underline;
}

nav .icons {
    display: flex;
    align-items: center;
}

nav .icons .icon {
    margin-left: 20px;
    display: flex;
    align-items: center;
    position: relative;
}

nav .icons .icon span {
    background-color: #e74c3c;
    color: white;
    border-radius: 50%;
    padding: 5px 10px;
    font-size: 0.9rem;
    position: absolute;
    top: -5px;
    right: -10px;
}
.search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 1rem;
            border: 2px solid #28a745;
            border-radius: 5px;
        }
        .search-bar button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            width: 250px;
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product-card .product-name {
            font-size: 1.2rem;
            margin: 10px 0;
        }
        .product-card .product-price {
            font-size: 1.1rem;
            color: #e74c3c;
            margin: 10px 0;
        }
        .product-card form {
            margin-top: 10px;
        }
        .product-card input[type="number"] {
            width: 50px;
            margin-right: 10px;
            padding: 5px;
            text-align: center;
        }
        .product-card button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .product-card button.add-to-cart {
            background-color: #28a745;
        }
        .product-card button.add-to-wishlist {
    background-color: #28a745; /* Green button */
}

        .product-card button:hover {
            opacity: 0.9;
        }
        .icon {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 1.2rem;
}

.icon a {
    text-decoration: none;
    color: inherit;
}

.icon span {
    background-color: #e74c3c;
    color: white;
    font-size: 0.8rem;
    padding: 2px 6px;
    border-radius: 50%;
}

    </style>

</head>
<body>
    
<nav>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="about.php">About</a>
        <a href="index.php">Home</a>
    </div>
    <div class="icons">
        <!-- Wishlist icon -->
        <div class="icon">
            <a href="wishlist.php" title="Wishlist">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-.06-.06a5.5 5.5 0 0 0-7.78 7.78l7.78 7.78 7.78-7.78a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </a>
            <span><?php echo $wishlist_count; ?></span>
        </div>

        <!-- Cart icon -->
        <div class="icon">
            <a href="cart.php" title="Cart">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </a>
            <span><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span> <!-- Only show the item count -->
        </div>
        <div class="header-buttons">
    
    <!-- <a href="order_history.php" class="btn">Order History</a> -->
</div>

        <a href="logout.php">Logout</a>
    </div>
</nav>
<h2>Welcome to the Dashboard, <?php echo htmlspecialchars($username); ?>!</h2>
<h2>Products</h2>
<div class="search-bar">
    <form method="GET" action="dashboard.php">
        <input type="text" name="search" placeholder="Search for products..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
</div>
<div class="product-list">
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="product-card">
        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <div class="product-name"><?php echo htmlspecialchars($row['name']); ?></div>
        <div class="product-price">$<?php echo number_format($row['price'], 2); ?></div>
        
        <!-- Add to Cart form -->
        <form method="POST" action="dashboard.php">
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <input type="number" name="quantity" min="1" value="1">
            <button type="submit" name="add_to_cart">Add to Cart</button>
        </form>
        
        <!-- Add to Wishlist form -->
        <form method="POST" action="dashboard.php">
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <button type="submit" name="add_to_wishlist">Add to Wishlist</button>
        </form>
    </div>
    <?php endwhile; ?>
</div>
</body>
</html>

    