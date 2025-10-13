<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

if (!isset($_POST['section_id'])) {
    echo json_encode(['success'=>false,'error'=>'Missing section_id']);
    exit;
}

$id = intval($_POST['section_id']);
$stmt = $conn->prepare("UPDATE tblsection SET is_deleted = 1 WHERE section_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>