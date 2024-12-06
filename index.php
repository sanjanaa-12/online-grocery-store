<?php 
ini_set('session.cookie_lifetime', 0); 
ini_set('session.gc_maxlifetime', 0);
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Grocery Store</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        h1 {
            background-color: green; /* Green background */
            color: white; /* White text */
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        body {
            margin: 0; /* Remove default margin */
            padding: 0;
        }
        nav {
            text-align: center;
            margin-bottom: 20px;
        }
        nav a {
            margin: 0 10px;
            text-decoration: none;
            color: white;
        }
        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>
        Welcome to the Online Grocery Store
        <?php if (isset($_SESSION['username'])): ?>
            , <?php echo htmlspecialchars($_SESSION['username']); ?>!
        <?php endif; ?>
    </h1>
    <nav>
        <?php if (isset($_SESSION['username'])): ?>
            <!-- Show these links only if the user is logged in -->
            <a href="dashboard.php">Dashboard</a>
            <a href="about.php">About</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <!-- Show these links only if the user is NOT logged in -->
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
            <a href="about.php">About</a>
        <?php endif; ?>
    </nav>

    <div class="main-content">
        <h2>Featured Products</h2>
        <div class="featured-products">
            <div class="product-card">
                <img src="images/apple.jpg" alt="Apples">
                <p>Apples</p>
                <p>Price: $2.00</p>
            </div>
            <div class="product-card">
                <img src="images/broccoli.jpg" alt="Broccoli">
                <p>Broccoli</p>
                <p>Price: $5.00</p>
            </div>
            <div class="product-card">
                <img src="images/carrot.jpg" alt="Carrots">
                <p>Carrots</p>
                <p>Price: $3.00</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Online Grocery Store | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
