<?php
session_start();
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... your existing insert code ...
    
    // Get the ID of the newly inserted record
    $new_id = $conn->insert_id;
    
    // Store in session for highlighting
    $_SESSION['new_student_id'] = $new_id;
    
    echo "success";
}
?>
