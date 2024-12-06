<?php
require 'db.php';
header("Cache-Control: no-store, no-cache, must-revalidate"); // Prevent caching
header("Pragma: no-cache");
header("Expires: 0");
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Initialize error variable
    $error = '';

    // Validate the input
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        // If username or email already exists
        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Redirect to login page after successful signup
                header('Location: login.php');
                exit();
            } else {
                // Generic error message
                $error = "Error creating account. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
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

        /* Container for Form */
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
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            margin: 0.8rem 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        /* Submit Button */
        button {
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

        button:hover {
            background-color: #0056b3;
        }

        /* Error Message */
        .message {
            padding: 10px;
            margin-bottom: 1rem;
            color: white;
            border-radius: 5px;
            text-align: center;
        }

        .message.error {
            background-color: #dc3545;
        }

        .message i {
            margin-left: 10px;
            cursor: pointer;
        }

        /* Link Styling */
        p {
            margin-top: 1rem;
        }

        p a {
            color: #007BFF;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .form-container {
                padding: 1.5rem;
            }

            h3 {
                font-size: 1.5rem;
            }

            button {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <section class="form-container">
        <form method="POST">
            <h3>Sign Up</h3>
            <input type="text" name="username" class="box" placeholder="Username" value="<?= isset($username) ? $username : '' ?>" required>
            <input type="email" name="email" class="box" placeholder="Email" value="<?= isset($email) ? $email : '' ?>" required>
            <input type="password" name="password" class="box" placeholder="Password" required>
            <button type="submit" class="btn">Sign Up</button>

            <!-- Display error message if validation fails -->
            <?php if (!empty($error)) echo "<div class='message error'><span>$error</span></div>"; ?>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </section>
</body>
</html>
