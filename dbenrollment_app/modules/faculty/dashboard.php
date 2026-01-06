<?php
/**
 * FACULTY DASHBOARD
 * Overview of courses, students, and recent activity
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
    die("Error: Faculty ID not found in session. Please contact administrator.");
}

// Get faculty info
$faculty_query = $conn->query("SELECT * FROM tblinstructor WHERE instructor_id = '$faculty_id'");
$faculty = $faculty_query->fetch_assoc();

// Get statistics
$stats = [];

// Total sections taught
$sections_result = $conn->query("SELECT COUNT(*) as total FROM tblsection WHERE instructor_id = '$faculty_id' AND is_deleted = 0");
$stats['sections'] = $sections_result->fetch_assoc()['total'];

// Total students across all sections
$students_result = $conn->query("
    SELECT COUNT(DISTINCT e.student_id) as total 
    FROM tblenrollment e
    JOIN tblsection s ON e.section_id = s.section_id
    WHERE s.instructor_id = '$faculty_id' AND e.is_deleted = 0
");
$stats['students'] = $students_result->fetch_assoc()['total'];

// Pending grades (students without grades)
$pending_result = $conn->query("
    SELECT COUNT(*) as total 
    FROM tblenrollment e
    JOIN tblsection s ON e.section_id = s.section_id
    WHERE s.instructor_id = '$faculty_id' 
    AND (e.letter_grade IS NULL OR e.letter_grade = '') 
    AND e.is_deleted = 0
");
$stats['pending_grades'] = $pending_result->fetch_assoc()['total'];

// Get recent enrollments in faculty's sections
$recent_enrollments = $conn->query("
    SELECT 
        st.first_name, st.last_name, st.student_no,
        c.course_code, sec.section_code,
        e.date_enrolled
    FROM tblenrollment e
    JOIN tblsection sec ON e.section_id = sec.section_id
    JOIN tblcourse c ON sec.course_id = c.course_id
    JOIN tblstudent st ON e.student_id = st.student_id
    WHERE sec.instructor_id = '$faculty_id'
    AND e.is_deleted = 0
    ORDER BY e.date_enrolled DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - Enrollment System</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/faculty_portal.css">
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/faculty_sidebar.php'); ?>

    <main class="main">
        <h1>Welcome, Prof. <?= htmlspecialchars($faculty['last_name'] ?? 'Faculty') ?>!</h1>
        <p style="color: #666; margin-bottom: 30px;">
            <?= htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']) ?> • 
            <?= htmlspecialchars($faculty['email']) ?>
        </p>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>📚 My Sections</h3>
                <p><?= $stats['sections'] ?></p>
                <small>Active sections this term</small>
            </div>
            <div class="stat-card">
                <h3>👥 Total Students</h3>
                <p><?= $stats['students'] ?></p>
                <small>Enrolled across all sections</small>
            </div>
            <div class="stat-card">
                <h3>⏳ Pending Grades</h3>
                <p><?= $stats['pending_grades'] ?></p>
                <small>Students awaiting grades</small>
            </div>
            <div class="stat-card">
                <h3>📅 Current Term</h3>
                <p>1st Sem</p>
                <small>SY 2025-2026</small>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="table-container">
            <h2>Recent Enrollments in Your Sections</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student No.</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Enrolled Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_enrollments && $recent_enrollments->num_rows > 0): ?>
                        <?php while($row = $recent_enrollments->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['student_no']) ?></td>
                                <td><strong><?= htmlspecialchars($row['course_code']) ?></strong></td>
                                <td><?= htmlspecialchars($row['section_code']) ?></td>
                                <td><?= date('M j, Y', strtotime($row['date_enrolled'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px; color: #888;">
                                No recent enrollments in your sections.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Quick Actions -->
        <div style="margin-top: 30px;">
            <h2>Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
                <a href="my_courses.php" style="padding: 20px; background: white; border-radius: 10px; text-decoration: none; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
                    <strong style="color: #7B1113;">📚 View My Courses</strong><br>
                    <small style="color: #666;">See all sections you teach</small>
                </a>
                <a href="grade_students.php" style="padding: 20px; background: white; border-radius: 10px; text-decoration: none; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
                    <strong style="color: #7B1113;">✏️ Grade Students</strong><br>
                    <small style="color: #666;">Submit or update grades</small>
                </a>
                <a href="view_enrolled.php" style="padding: 20px; background: white; border-radius: 10px; text-decoration: none; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
                    <strong style="color: #7B1113;">👥 View Students</strong><br>
                    <small style="color: #666;">See enrolled students</small>
                </a>
            </div>
        </div>
    </main>
</body>
</html>