<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

if (!isset($_POST['enrollment_id'])) {
    echo json_encode(['success'=>false,'error'=>'Missing enrollment_id']);
    exit;
}

$id = intval($_POST['enrollment_id']);
$stmt = $conn->prepare("UPDATE tblenrollment SET is_deleted = 1 WHERE enrollment_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>