<?php
/**
 * LOGIN PAGE - DBA Enrollment System
 * Handles authentication for Admin, Faculty, and Students
 */
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header("Location: modules/student/index.php");
    } elseif ($role === 'faculty') {
        header("Location: modules/faculty/dashboard.php");
    } elseif ($role === 'student') {
        header("Location: modules/student_portal/dashboard.php");
    }
    exit();
}

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'dbenrollment';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_input = $_POST['password'];
    
    if (empty($username) || empty($password_input)) {
        $error = "Please fill in all fields.";
    } else {
        // Check in tbluser table
        $stmt = $conn->prepare("SELECT user_id, username, password, role, full_name, reference_id FROM tbluser WHERE username = ? AND is_active = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password_input, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['reference_id'] = $user['reference_id'];
                
                // Redirect based on role
                if ($user['role'] === 'student') {
                    $_SESSION['student_id'] = $user['reference_id'];
                    header("Location: modules/student_portal/dashboard.php");
                } elseif ($user['role'] === 'admin') {
                    header("Location: modules/student/index.php");
                } elseif ($user['role'] === 'faculty') {
                    $_SESSION['reference_id'] = $user['reference_id'];
                    header("Location: modules/faculty/dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DBA Enrollment System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #7B1113 0%, #1E1E1E 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .login-left {
            background: linear-gradient(135deg, #7B1113 0%, #9C1E1F 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-left h1 { font-size: 2rem; margin-bottom: 20px; color: #FFCC00; }
        .login-left p { font-size: 1rem; line-height: 1.6; opacity: 0.9; }
        .login-right { padding: 60px 40px; display: flex; flex-direction: column; justify-content: center; }
        .login-right h2 { font-size: 1.8rem; color: #7B1113; margin-bottom: 10px; }
        .login-right p { color: #666; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 0.9rem; }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus { outline: none; border-color: #7B1113; box-shadow: 0 0 0 3px rgba(123, 17, 19, 0.1); }
        .alert {
            padding: 12px 16px;
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border-left: 4px solid #ef4444;
        }
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7B1113 0%, #9C1E1F 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(123, 17, 19, 0.3);
        }
        .login-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(123, 17, 19, 0.4); }
        @media (max-width: 768px) {
            .login-container { grid-template-columns: 1fr; }
            .login-left, .login-right { padding: 40px 30px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <h1>DBA Enrollment Management System</h1>
            <p>Welcome to the official enrollment portal. Sign in with your credentials to access your dashboard.</p>
            <div style="margin-top: 40px;">
                <h4 style="color: #FFCC00; margin-bottom: 15px;">Access Levels:</h4>
                <p style="font-size: 0.9rem; margin-bottom: 10px;">👤 <strong>Students:</strong> Enroll in courses and view grades</p>
                <p style="font-size: 0.9rem; margin-bottom: 10px;">👨‍🏫 <strong>Faculty:</strong> Manage courses and grade students</p>
                <p style="font-size: 0.9rem;">🛡️ <strong>Admin:</strong> Full system access and maintenance</p>
            </div>
        </div>

        <div class="login-right">
            <h2>Sign In</h2>
            <p>Enter your credentials to continue</p>

            <?php if (!empty($error)): ?>
                <div class="alert">✗ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-btn">Sign In</button>
            </form>
            <div class="roles-info" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <p style="font-size: 0.85rem; color: #666;">Contact your administrator for account creation.</p>
            </div>
        </div>
    </div>
</body>
</html>