<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['section_id'])) {
        throw new Exception("Missing section ID");
    }

    $id = intval($_GET['section_id']);
    $stmt = $conn->prepare("SELECT section_id, section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity, year_level FROM tblsection WHERE section_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                "success" => true,
                "data" => $row
            ]);
            exit;
        } else {
            throw new Exception("Section not found");
        }
    } else {
        throw new Exception("Failed to fetch section data");
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in get_section.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}
?>