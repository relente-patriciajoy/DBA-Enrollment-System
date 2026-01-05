<?php
include('../../config/database.php');
session_start();

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../login.php");
    exit();
}

// CRITICAL: Ensure this matches the ID stored during login (likely 23 for Patricia)
$student_id = $_SESSION['student_id'] ?? $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - Enrollment System</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/student_portal.css">
    <style>
        .badge-status { padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; background: #e2e8f0; color: #475569; }
    </style>
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/student_sidebar.php'); ?>

    <main class="main">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Student'); ?>!</h1>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Enrolled Courses</h3>
                <p>
                    <?php
                    // Counts actual rows in tblenrollment for this student
                    $count_res = $conn->query("SELECT COUNT(*) as total FROM tblenrollment WHERE student_id = '$student_id' AND is_deleted = 0");
                    $count_data = $count_res->fetch_assoc();
                    echo $count_data['total'] ?? 0;
                    ?>
                </p>
            </div>
            <div class="stat-card">
                <h3>Academic Year</h3>
                <p>2025-2026</p>
            </div>
            <div class="stat-card">
                <h3>Year Level</h3>
                <p>
                    <?php
                    // Pulls the year_level directly from Patricia's student record
                    $student_res = $conn->query("SELECT year_level FROM tblstudent WHERE student_id = '$student_id'");
                    $s_data = $student_res->fetch_assoc();
                    echo htmlspecialchars($s_data['year_level'] ?? '3rd Year');
                    ?>
                </p>
            </div>
        </div>

        <div class="table-container">
            <h2>Recently Enrolled</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Section</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetches the 3 most recent enrollments
                    $recent = $conn->query("SELECT c.course_code, sec.section_code, e.status 
                                          FROM tblenrollment e 
                                          JOIN tblsection sec ON e.section_id = sec.section_id 
                                          JOIN tblcourse c ON sec.course_id = c.course_id 
                                          WHERE e.student_id = '$student_id'
                                          ORDER BY e.date_enrolled DESC LIMIT 3");

                    if ($recent && $recent->num_rows > 0):
                        while($row = $recent->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['course_code']) ?></strong></td>
                                <td><?= htmlspecialchars($row['section_code']) ?></td>
                                <td><span class="badge-status"><?= htmlspecialchars($row['status']) ?></span></td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding: 20px; color: #888;">
                                No recent enrollment records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>