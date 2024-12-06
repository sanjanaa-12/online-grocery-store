<?php
ini_set('session.cookie_lifetime', 0); 
ini_set('session.gc_maxlifetime', 0);
session_start();
require 'db.php';
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate"); // Prevent caching
header("Pragma: no-cache");
header("Expires: 0");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            setcookie("user", $username, time() + (86400 * 30), "/");
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Add inline JavaScript for form validation -->
    <script>
        // Client-side validation for Login Form
        function validateLoginForm() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;

            // Check if username is empty
            if (username == "") {
                alert("Username is required");
                return false;
            }

            // Check if password is empty
            if (password == "") {
                alert("Password is required");
                return false;
            }

            // Optionally, you can validate if the username is in email format
            // var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            // if (!emailRegex.test(username)) {
            //     alert("Please enter a valid email address");
            //     return false;
            // }

            // If all validations pass, return true
            return true;
        }
    </script>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #333;
        }

        /* Form Container */
        .form-container {
            max-width: 500px;
            width: 90%;
            padding: 2rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* Form Header */
        h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Input Fields */
        .box {
            width: 100%;
            padding: 0.8rem;
            margin: 0.8rem 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        /* Submit Button */
        .btn {
            width: 100%;
            padding: 0.8rem;
            margin-top: 1rem;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Links and Messages */
        p {
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        p a {
            color: #007BFF;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Notification Messages */
        .message {
            padding: 10px;
            margin-bottom: 1rem;
            color: white;
            border-radius: 5px;
            font-size: 0.9rem;
            text-align: center;
        }

        .message.error {
            background-color: #dc3545;
        }

        .message.success {
            background-color: #28a745;
        }

        .message i {
            margin-left: 10px;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .form-container {
                padding: 1.5rem;
            }

            h3 {
                font-size: 1.5rem;
            }

            .btn {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <section class="form-container">
        <form method="POST" onsubmit="return validateLoginForm()">
            <h3>Login</h3>
            <input type="text" name="username" class="box" id="username" placeholder="Username" required>
            <input type="password" name="password" class="box" id="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
            <?php if (!empty($error)) echo "<div class='message error'><span>$error</span><i class='fas fa-times' onclick='this.parentElement.remove();'></i></div>"; ?>
            <p>Don't have an account? <a href="signup.php">Register here</a></p>
        </form>
    </section>
</body>
</html>
