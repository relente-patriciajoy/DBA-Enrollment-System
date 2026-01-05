<?php
include('../../config/database.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../login.php");
    exit();
}

$student_id = $_SESSION['student_id'] ?? $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Enrolled Courses</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/student_portal.css">
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/student_sidebar.php'); ?>

    <main class="main">
        <h1>My Enrolled Courses</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Schedule</th>
                        <th>Professor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to join Course, Section, and Instructor tables
                    $sql = "SELECT c.course_code, c.course_title,
                                sec.section_code,
                                inst.first_name, inst.last_name,
                                e.status
                            FROM tblenrollment e
                            JOIN tblsection sec ON e.section_id = sec.section_id
                            JOIN tblcourse c ON sec.course_id = c.course_id
                            LEFT JOIN tblinstructor inst ON sec.instructor_id = inst.instructor_id
                            WHERE e.student_id = '$student_id' AND e.is_deleted = 0";

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()):
                            $prof_name = ($row['first_name']) ? $row['first_name'] . " " . $row['last_name'] : "TBA";
                        ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['course_code']) ?></strong><br>
                                    <small style="color: #666;"><?= htmlspecialchars($row['course_title'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['section_code']) ?></td>
                                <td><?= htmlspecialchars($row['schedule'] ?? 'No schedule set') ?></td>
                                <td><?= htmlspecialchars($prof_name) ?></td>
                                <td><span class="badge"><?= htmlspecialchars($row['status']) ?></span></td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px;">
                                You haven't enrolled in any courses yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>