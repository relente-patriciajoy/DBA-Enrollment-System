<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');
requireRoleAjax('admin');
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect all 11 fields (10 for update + 1 for the WHERE clause)
    $id = intval($_POST['section_id']);
    $code = $_POST['section_code'];
    $course = intval($_POST['course_id']);
    $term = intval($_POST['term_id']);
    $instructor = intval($_POST['instructor_id']);
    $pattern = $_POST['day_pattern'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $room = intval($_POST['room_id']);
    $capacity = intval($_POST['max_capacity']);
    $year = intval($_POST['year_level']);

    // The SQL has 10 SET fields and 1 WHERE field = 11 total variables
    $sql = "UPDATE tblsection SET
            section_code = ?, course_id = ?, term_id = ?, instructor_id = ?,
            day_pattern = ?, start_time = ?, end_time = ?, room_id = ?,
            max_capacity = ?, year_level = ?
            WHERE section_id = ?";
    
    $stmt = $conn->prepare($sql);

    // FIX: Match the string exactly to the 11 variables below
    // s (code), i (course), i (term), i (inst), s (pattern), s (start), s (end), i (room), i (cap), i (year), i (id)
    $stmt->bind_param("siiisssiiii", $code, $course, $term, $instructor, $pattern, $start, $end, $room, $capacity, $year, $id);

    if ($stmt->execute()) {
        ob_clean();
        echo json_encode(['success' => true]);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}
$conn->close();