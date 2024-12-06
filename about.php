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
    <title>About Us - Online Grocery Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
        }
        header h1 {
            margin: 0;
        }
        nav {
            text-align: center;
            margin: 20px 0;
        }
        nav a {
            text-decoration: none;
            color: #28a745;
            margin: 0 15px;
            font-weight: bold;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .about-section {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .about-section img {
            max-width: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .about-section p {
            flex: 1;
            line-height: 1.6;
        }
        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #28a745;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>About Online Grocery Store</h1>
    </header>

    <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- If user is logged in, show Home, Dashboard, and Logout options -->
            <a href="index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <!-- If user is not logged in, show Home, Login, and Sign Up options -->
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </nav>

    <div class="container">
        <div class="about-section">
            <img src="images/ogs.jpg" alt="Online Grocery Store">
            <p>
                Welcome to the Online Grocery Store! Our mission is to make grocery shopping simple, quick, and hassle-free. We provide a wide variety of fresh fruits, vegetables, and daily essentials delivered directly to your doorstep.
                <br><br>
                With our user-friendly interface and secure shopping platform, we strive to save you time while ensuring quality. Whether you're stocking up on pantry staples or looking for organic produce, we’ve got you covered. 
                <br><br>
                Enjoy exclusive discounts, a seamless shopping experience, and a customer-first approach. Shop smart, shop Online Grocery Store!
            </p>
        </div>
    </div>

    <footer>
        &copy; 2024 Online Grocery Store | Designed with ❤️ for your convenience.
    </footer>
</body>
</html>
