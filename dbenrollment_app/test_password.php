<?php
/**
 * PASSWORD TESTER & RESET UTILITY
 * Use this to test and reset passwords for your enrollment system
 */

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'dbenrollment';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ========================================
// SECTION 1: TEST EXISTING PASSWORD
// ========================================
echo "<h2>TEST EXISTING PASSWORD</h2>";

$test_username = 'faculty1';  // Change this to the username you want to test
$test_password = 'faculty123'; // Change this to the password you're trying

$stmt = $conn->prepare("SELECT user_id, username, password, role FROM tbluser WHERE username = ?");
$stmt->bind_param("s", $test_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "✓ User found: " . $user['username'] . " (Role: " . $user['role'] . ")<br>";
    echo "Stored hash: " . $user['password'] . "<br><br>";

    if (password_verify($test_password, $user['password'])) {
        echo "<strong style='color: green;'>✓ PASSWORD MATCHES! Login should work.</strong><br>";
    } else {
        echo "<strong style='color: red;'>✗ PASSWORD DOES NOT MATCH!</strong><br>";
        echo "The password you entered does not match the hash in the database.<br><br>";
    }
} else {
    echo "<strong style='color: red;'>✗ User not found in database.</strong><br>";
}

echo "<hr>";

// ========================================
// SECTION 2: RESET PASSWORD
// ========================================
echo "<h2>RESET PASSWORD (Uncomment to use)</h2>";
echo "<pre>";
echo "To reset the password, uncomment the code below and set your new password:\n\n";
echo "/*\n";
echo "\$reset_username = 'faculty1';\n";
echo "\$new_password = 'faculty123';  // Your new password\n";
echo "\$hashed = password_hash(\$new_password, PASSWORD_DEFAULT);\n\n";
echo "\$stmt = \$conn->prepare(\"UPDATE tbluser SET password = ? WHERE username = ?\");\n";
echo "\$stmt->bind_param(\"ss\", \$hashed, \$reset_username);\n";
echo "\$stmt->execute();\n";
echo "echo \"✓ Password updated successfully!\";\n";
echo "*/\n";
echo "</pre>";

// Uncomment this section to actually reset the password:

$reset_username = 'faculty1';
$new_password = 'faculty123';  // Change this to your desired password
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE tbluser SET password = ? WHERE username = ?");
$stmt->bind_param("ss", $hashed, $reset_username);
if ($stmt->execute()) {
    echo "<strong style='color: green;'>✓ Password for '$reset_username' has been reset to '$new_password'</strong><br>";
} else {
    echo "<strong style='color: red;'>✗ Error updating password</strong><br>";
}


echo "<hr>";

// ========================================
// SECTION 3: VIEW ALL USERS
// ========================================
echo "<h2>ALL USERS IN SYSTEM</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>User ID</th><th>Username</th><th>Role</th><th>Reference ID</th><th>Active</th></tr>";

$all_users = $conn->query("SELECT user_id, username, role, reference_id, is_active FROM tbluser");
while ($row = $all_users->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['user_id'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "<td>" . $row['reference_id'] . "</td>";
    echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Tester</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; }
        h2 { color: #7B1113; border-bottom: 2px solid #7B1113; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #7B1113; color: white; padding: 10px; }
        td { padding: 8px; }
        tr:nth-child(even) { background: #f5f5f5; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
</body>
</html>