<?php
/**
 * Admin Login Page
 * Student Name: hanniba
 * Student ID: hanniba
 * 
 * Simple password-protected login for admin panel
 */

session_start();

// Hardcoded credentials (for assignment purposes)
$admin_username = 'admin';
$admin_password = 'admin123';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

// If already logged in, redirect to index
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Graphics Cards Store</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 2em;
            font-weight: 800;
            color: #333;
            margin: 0 0 10px 0;
        }

        .login-header p {
            color: #666;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #eeeeee;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #21759b;
        }

        .error-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #ffeaa7;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #21759b;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #185a7a;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #21759b;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Graphics Cards Store Management</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>

        <a href="../products.php" class="back-link">← Back to Products</a>
    </div>
</body>
</html>

