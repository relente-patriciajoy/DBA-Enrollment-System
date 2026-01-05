<?php
/**
 * Role-Based Access Control
 * Functions to check user roles
 */

if (!function_exists('requireRole')) {
    function requireRole($required_role) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role) {
            die("
                <html>
                <head>
                    <title>Access Denied</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                            background: linear-gradient(135deg, #7B1113 0%, #1E1E1E 100%);
                        }
                        .error-box {
                            background: white;
                            padding: 40px;
                            border-radius: 10px;
                            text-align: center;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                        }
                        h1 { color: #7B1113; margin-bottom: 10px; }
                        p { color: #666; margin-bottom: 20px; }
                        a {
                            display: inline-block;
                            padding: 10px 20px;
                            background: #7B1113;
                            color: white;
                            text-decoration: none;
                            border-radius: 5px;
                        }
                        a:hover { background: #9C1E1F; }
                    </style>
                </head>
                <body>
                    <div class='error-box'>
                        <h1>🚫 Access Denied</h1>
                        <p>You don't have permission to access this page.</p>
                        <p>Required role: <strong>{$required_role}</strong></p>
                        <a href='../../index.php'>Back to Login</a>
                    </div>
                </body>
                </html>
            ");
        }
    }
}

if (!function_exists('requireAnyRole')) {
    function requireAnyRole($allowed_roles) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
            $roles = implode(', ', $allowed_roles);
            die("
                <html>
                <head>
                    <title>Access Denied</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                            background: linear-gradient(135deg, #7B1113 0%, #1E1E1E 100%);
                        }
                        .error-box {
                            background: white;
                            padding: 40px;
                            border-radius: 10px;
                            text-align: center;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                        }
                        h1 { color: #7B1113; margin-bottom: 10px; }
                        p { color: #666; margin-bottom: 20px; }
                        a {
                            display: inline-block;
                            padding: 10px 20px;
                            background: #7B1113;
                            color: white;
                            text-decoration: none;
                            border-radius: 5px;
                        }
                        a:hover { background: #9C1E1F; }
                    </style>
                </head>
                <body>
                    <div class='error-box'>
                        <h1>🚫 Access Denied</h1>
                        <p>You don't have permission to access this page.</p>
                        <p>Allowed roles: <strong>{$roles}</strong></p>
                        <a href='../../index.php'>Back to Login</a>
                    </div>
                </body>
                </html>
            ");
        }
    }
}

/**
 * AJAX-friendly Role Check
 * Returns JSON instead of a full HTML error page
 */
if (!function_exists('requireRoleAjax')) {
    function requireRoleAjax($required_role) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role) {
            // Ensure no whitespace is sent before this JSON
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            echo json_encode([
                "success" => false,
                "error" => "Unauthorized: " . $required_role . " access required."
            ]);
            exit;
        }
    }
}
?>