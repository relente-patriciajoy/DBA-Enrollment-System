<!-- Add Student AJAX MODAL -->
<?php
// Start session at the top
session_start();
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    // Log received data for debugging
    error_log("Received POST data: " . print_r($_POST, true));

    // Validate required fields
    if (empty($_POST['student_no']) || empty($_POST['last_name']) || 
        empty($_POST['first_name']) || empty($_POST['email'])) {
        throw new Exception("Please fill in all required fields.");
    }

    $stmt = $conn->prepare("INSERT INTO tblstudent 
        (student_no, last_name, first_name, email, gender, birthdate, year_level, program_id, is_deleted)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssssi", 
        $_POST['student_no'],
        $_POST['last_name'],
        $_POST['first_name'],
        $_POST['email'],
        $_POST['gender'],
        $_POST['birthdate'],
        $_POST['year_level'],
        $_POST['program_id']
    );

    if ($stmt->execute()) {
        $_SESSION['new_student_id'] = $conn->insert_id;
        echo json_encode([
            "success" => true,
            "message" => "Student added successfully",
            "student_id" => $conn->insert_id
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
