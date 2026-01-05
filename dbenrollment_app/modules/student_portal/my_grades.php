<?php 
include('../../config/database.php');
session_start();

// Security check: Ensure student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Academic Grades - Enrollment System</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/student_portal.css">
    <style>
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; }
        .bg-success { background: #dcfce7; color: #166534; border: 1px solid #166534; }
        .bg-danger { background: #fee2e2; color: #991b1b; border: 1px solid #991b1b; }
        .bg-secondary { background: #f3f4f6; color: #374151; border: 1px solid #374151; }
        .main h1 { color: #7B1113; margin-bottom: 30px; }
    </style>
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/student_sidebar.php'); ?>

    <main class="main">
        <h1>My Academic Grades</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT c.*, e.letter_grade, e.status
                            FROM tblenrollment e
                            JOIN tblsection sec ON e.section_id = sec.section_id
                            JOIN tblcourse c ON sec.course_id = c.course_id
                            WHERE e.student_id = '$student_id' AND e.is_deleted = 0";

                    $result = $conn->query($sql);
                    if (!$result) { echo "SQL Error: " . $conn->error; }

                    if ($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['course_code']) ?></strong></td>
                                <td><?= htmlspecialchars($row['course_title'] ?? $row['description'] ?? 'No Title Found') ?></td>
                                <td><strong><?= $row['letter_grade'] ?: 'N/A' ?></strong></td>
                                <td>
                                    <?php
                                        $grade = $row['letter_grade'];
                                        if (in_array($grade, ['A', 'B', 'C', '1.0', '1.25', '1.5', '1.75', '2.0', '2.25', '2.5', '2.75', '3.0'])) {
                                            echo "<span class='badge bg-success'>Passed</span>";
                                        } else if ($grade == 'F' || $grade == '5.0') {
                                            echo "<span class='badge bg-danger'>Failed</span>";
                                        } else {
                                            echo "<span class='badge bg-secondary'>Ongoing</span>";
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 40px; color: #666;">
                                No academic records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>