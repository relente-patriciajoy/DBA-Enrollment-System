<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['instructor_id'])) {
        throw new Exception("Invalid instructor ID.");
    }

    $stmt = $conn->prepare("UPDATE tblinstructor 
                           SET last_name=?, first_name=?, email=?, dept_id=?
                           WHERE instructor_id=?");

    $stmt->bind_param("sssii", 
        $_POST['last_name'],
        $_POST['first_name'],
        $_POST['email'],
        $_POST['dept_id'],
        $_POST['instructor_id']
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Instructor updated successfully"
        ]);
    } else {
        throw new Exception("Failed to update instructor.");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
