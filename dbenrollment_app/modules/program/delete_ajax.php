<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

if (!isset($_POST['program_id'])) {
    echo json_encode(['success'=>false,'error'=>'Missing id']);
    exit;
}
$id = intval($_POST['program_id']); // Changed from $_POST['id']
$stmt = $conn->prepare("UPDATE tblprogram SET is_deleted = 1 WHERE program_id = ?");
$stmt->bind_param("i",$id);
if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}
$stmt->close();
$conn->close();
?>