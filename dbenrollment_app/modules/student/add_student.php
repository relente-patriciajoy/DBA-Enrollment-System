<?php
session_start();
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ...existing insert code...
    $new_id = $conn->insert_id;
    $_SESSION['new_student_id'] = $new_id;
    echo "success";
}
?>
