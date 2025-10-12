<?php
session_start(); // Add session start
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM tblstudent 
        WHERE (student_no LIKE '%$search%' 
        OR last_name LIKE '%$search%' 
        OR first_name LIKE '%$search%')
        AND is_deleted = 0 
        ORDER BY student_id DESC, last_name ASC 
        LIMIT 100";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $highlightClass = isset($_SESSION['new_student_id']) && $_SESSION['new_student_id'] == $row['student_id'] ? 'new-row' : '';
        echo "<tr class='{$highlightClass}'>
            <td>{$row['student_no']}</td>
            <td>{$row['last_name']}</td>
            <td>{$row['first_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['gender']}</td>
            <td>{$row['birthdate']}</td>
            <td>{$row['year_level']}</td>
            <td>{$row['program_id']}</td>
            <td class='text-center'>
                <button class='btn btn-sm btn-warning' onclick='editStudent({$row['student_id']})'>Edit</button>
                <button class='btn btn-sm btn-danger' onclick='deleteStudent({$row['student_id']})'>Delete</button>
            </td>
        </tr>";
    }
    // Only unset if it exists
    if(isset($_SESSION['new_student_id'])) {
        unset($_SESSION['new_student_id']);
    }
} else {
    echo "<tr><td colspan='9' class='text-center'>No records found.</td></tr>";
}
?>