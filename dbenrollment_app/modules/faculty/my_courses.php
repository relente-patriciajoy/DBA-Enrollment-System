<?php
/**
 * FACULTY - MY COURSES
 * View all sections taught by this faculty member
 */
include('../../config/database.php');
session_start();

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty') {
    header("Location: ../../index.php");
    exit();
}

$faculty_id = $_SESSION['reference_id'] ?? null;

if (!$faculty_id) {
    die("Error: Faculty ID not found in session.");
}

// Get all sections taught by this faculty
$sections_query = $conn->query("
    SELECT 
        s.section_id,
        s.section_code,
        s.day_pattern,
        s.start_time,
        s.end_time,
        s.max_capacity,
        c.course_code,
        c.course_title,
        c.units,
        r.room_code,
        r.building,
        t.term_code,
        COUNT(e.enrollment_id) as enrolled_count
    FROM tblsection s
    JOIN tblcourse c ON s.course_id = c.course_id
    LEFT JOIN tblroom r ON s.room_id = r.room_id
    LEFT JOIN tblterm t ON s.term_id = t.term_id
    LEFT JOIN tblenrollment e ON s.section_id = e.section_id AND e.is_deleted = 0
    WHERE s.instructor_id = '$faculty_id' AND s.is_deleted = 0
    GROUP BY s.section_id
    ORDER BY t.term_code DESC, c.course_code ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Faculty Portal</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/faculty_portal.css">
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/faculty_sidebar.php'); ?>

    <main class="main">
        <h1>📚 My Teaching Load</h1>
        <p style="color: #666; margin-bottom: 30px;">All sections you are currently teaching</p>

        <div class="table-container">
            <?php if ($sections_query && $sections_query->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Section</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <th>Term</th>
                            <th>Enrolled</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $sections_query->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['course_code']) ?></strong><br>
                                    <small style="color: #666;"><?= htmlspecialchars($row['course_title']) ?></small><br>
                                    <small style="color: #999;"><?= $row['units'] ?> units</small>
                                </td>
                                <td><?= htmlspecialchars($row['section_code']) ?></td>
                                <td>
                                    <?= htmlspecialchars($row['day_pattern'] ?? 'TBA') ?><br>
                                    <small style="color: #666;">
                                        <?php 
                                            if ($row['start_time'] && $row['end_time']) {
                                                echo date('g:i A', strtotime($row['start_time'])) . ' - ' . 
                                                     date('g:i A', strtotime($row['end_time']));
                                            } else {
                                                echo 'Time TBA';
                                            }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($row['room_code']): ?>
                                        <?= htmlspecialchars($row['room_code']) ?><br>
                                        <small style="color: #666;"><?= htmlspecialchars($row['building']) ?></small>
                                    <?php else: ?>
                                        <span style="color: #999;">Room TBA</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($row['term_code'] ?? 'Current') ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= $row['enrolled_count'] ?></strong> / <?= $row['max_capacity'] ?? 'N/A' ?>
                                </td>
                                <td>
                                    <a href="view_enrolled.php?section_id=<?= $row['section_id'] ?>" 
                                       class="btn btn-primary btn-sm" 
                                       style="display: inline-block; padding: 6px 12px; text-decoration: none; border-radius: 5px;">
                                        👥 View Students
                                    </a>
                                    <a href="grade_students.php?section_id=<?= $row['section_id'] ?>" 
                                       class="btn btn-warning btn-sm"
                                       style="display: inline-block; padding: 6px 12px; text-decoration: none; border-radius: 5px; margin-top: 5px;">
                                        ✏️ Grade
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center; padding: 60px 20px; background: white; border-radius: 10px;">
                    <p style="font-size: 3rem; margin: 0;">📚</p>
                    <h3 style="color: #666; margin: 20px 0 10px;">No Sections Assigned</h3>
                    <p style="color: #999;">You don't have any sections assigned for this term yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>