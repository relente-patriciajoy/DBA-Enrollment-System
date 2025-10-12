<?php
session_start();
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    error_log("Received POST data: " . print_r($_POST, true));

    if (empty($_POST['last_name']) || empty($_POST['first_name']) || empty($_POST['email'])) {
        throw new Exception("Please fill in all required fields.");
    }

    $stmt = $conn->prepare("INSERT INTO tblinstructor 
            (last_name, first_name, email, dept_id, is_deleted)
            VALUES (?, ?, ?, ?, 0)");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssi", 
        $_POST['last_name'],
        $_POST['first_name'],
        $_POST['email'],
        $_POST['dept_id']
    );

    if ($stmt->execute()) {
        $_SESSION['new_instructor_id'] = $conn->insert_id;
        echo json_encode([
            "success" => true,
            "message" => "Instructor added successfully",
            "instructor_id" => $conn->insert_id
        ]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in add_ajax.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
