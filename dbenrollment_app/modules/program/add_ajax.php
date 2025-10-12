<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['program_code']) || empty($_POST['program_name']) || empty($_POST['dept_id'])) {
        throw new Exception("Please fill in all required fields.");
    }

    $program_code = $conn->real_escape_string($_POST['program_code']);
    $program_name = $conn->real_escape_string($_POST['program_name']);
    $dept_id = intval($_POST['dept_id']);

    $stmt = $conn->prepare("INSERT INTO tblprogram (program_code, program_name, dept_id, is_deleted) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("ssi", $program_code, $program_name, $dept_id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $newId = $conn->insert_id;
    $res = $conn->query("SELECT p.program_id, p.program_code, p.program_name, p.dept_id, d.dept_name
                         FROM tblprogram p LEFT JOIN tbldepartment d ON p.dept_id = d.dept_id
                         WHERE p.program_id = $newId LIMIT 1");
    $data = $res->fetch_assoc();

    echo json_encode([
        'success' => true,
        'program_id' => $data['program_id'],
        'program_code' => $data['program_code'],
        'program_name' => $data['program_name'],
        'dept_id' => $data['dept_id'],
        'dept_name' => $data['dept_name']
    ]);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}