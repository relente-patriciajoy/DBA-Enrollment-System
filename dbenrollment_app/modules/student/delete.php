<?php
include_once '../../config/database.php';

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $sql = "DELETE FROM tblstudent WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>