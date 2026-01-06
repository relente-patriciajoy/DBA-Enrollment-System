<?php
/**
 * FACULTY - VIEW ENROLLED STUDENTS (IMPROVED UX)
 * See all students enrolled - with section selector
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
    die("Error: Faculty ID not found in session. <a href='../../index.php'>Return to Login</a>");
}

// Get the selected section (from URL or default to first section)
$selected_section_id = $_GET['section_id'] ?? null;

// Get all sections taught by this faculty - FIXED with correct column names
$sections_stmt = $conn->prepare("
    SELECT 
        s.section_id,
        s.section_code,
        s.day_pattern,
        s.start_time,
        s.end_time,
        s.room_id,
        c.course_code,
        c.course_title,
        c.units,
        t.term_code,
        COUNT(DISTINCT e.student_id) as enrolled_count
    FROM tblsection s
    JOIN tblcourse c ON s.course_id = c.course_id
    LEFT JOIN tblterm t ON s.term_id = t.term_id
    LEFT JOIN tblenrollment e ON s.section_id = e.section_id AND e.is_deleted = 0
    WHERE s.instructor_id = ? AND s.is_deleted = 0
    GROUP BY s.section_id
    ORDER BY c.course_code ASC, s.section_code ASC
");
$sections_stmt->bind_param("i", $faculty_id);
$sections_stmt->execute();
$sections = $sections_stmt->get_result();

// Store sections in array for later use
$sections_array = [];
while ($row = $sections->fetch_assoc()) {
    $sections_array[] = $row;
    // If no section selected, select the first one
    if ($selected_section_id === null) {
        $selected_section_id = $row['section_id'];
    }
}

// Get details of selected section
$selected_section = null;
foreach ($sections_array as $section) {
    if ($section['section_id'] == $selected_section_id) {
        $selected_section = $section;
        break;
    }
}

// Get enrolled students for selected section
$students = null;
if ($selected_section_id) {
    $students_stmt = $conn->prepare("
        SELECT
            e.enrollment_id,
            e.letter_grade,
            e.status,
            e.date_enrolled,
            st.student_id,
            st.student_no,
            st.first_name,
            st.last_name,
            st.email,
            st.year_level,
            p.program_code
        FROM tblenrollment e
        JOIN tblstudent st ON e.student_id = st.student_id
        LEFT JOIN tblprogram p ON st.program_id = p.program_id
        WHERE e.section_id = ? AND e.is_deleted = 0
        ORDER BY st.last_name ASC, st.first_name ASC
    ");
    $students_stmt->bind_param("i", $selected_section_id);
    $students_stmt->execute();
    $students = $students_stmt->get_result();
}

// Helper function to format time
function formatTime($time) {
    if (empty($time)) return '';
    return date('g:i A', strtotime($time));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - Faculty Portal</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/faculty_portal.css">
    <style>
        .section-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .section-tab {
            padding: 15px 25px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 200px;
            text-align: center;
        }
        .section-tab:hover {
            border-color: #7B1113;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .section-tab.active {
            background: linear-gradient(135deg, #7B1113 0%, #9C1E1F 100%);
            color: white;
            border-color: #7B1113;
        }
        .section-tab .course-code {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .section-tab .student-count {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        .section-info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #7B1113;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-warning { background: #fff3cd; color: #856404; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #7B1113;
            color: white;
        }
        .btn-primary:hover {
            background: #5a0d0f;
        }
    </style>
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/faculty_sidebar.php'); ?>

    <main class="main">
        <h1>👥 View Students by Section</h1>
        <p style="color: #666; margin-bottom: 30px;">Select a section to view enrolled students</p>

        <?php if (count($sections_array) > 0): ?>
            <!-- Section Tabs -->
            <div class="section-tabs">
                <?php foreach ($sections_array as $section): ?>
                    <a href="?section_id=<?= $section['section_id'] ?>"
                       class="section-tab <?= $section['section_id'] == $selected_section_id ? 'active' : '' ?>">
                        <div class="course-code">
                            <?= htmlspecialchars($section['course_code']) ?> -
                            <?= htmlspecialchars($section['section_code']) ?>
                        </div>
                        <div class="student-count">
                            👥 <?= $section['enrolled_count'] ?> student<?= $section['enrolled_count'] != 1 ? 's' : '' ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($selected_section): ?>
                <!-- Selected Section Info -->
                <div class="section-info-card">
                    <h2 style="color: #7B1113; margin: 0 0 10px 0;">
                        <?= htmlspecialchars($selected_section['course_code']) ?> -
                        <?= htmlspecialchars($selected_section['section_code']) ?>
                    </h2>
                    <p style="color: #666; margin: 0 0 10px 0;">
                        <?= htmlspecialchars($selected_section['course_title']) ?>
                        (<?= htmlspecialchars($selected_section['units']) ?> units) •
                        <?= htmlspecialchars($selected_section['term_code'] ?? 'Current Term') ?>
                    </p>
                    <?php if (!empty($selected_section['day_pattern'])): ?>
                        <p style="color: #666; margin: 0;">
                            🕐 <?= htmlspecialchars($selected_section['day_pattern']) ?>
                            <?= formatTime($selected_section['start_time']) ?> - <?= formatTime($selected_section['end_time']) ?>
                            <?php if ($selected_section['room_id']): ?>
                                • 📍 Room <?= htmlspecialchars($selected_section['room_id']) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <div class="action-buttons">
                        <a href="grade_students.php?section_id=<?= $selected_section['section_id'] ?>" class="btn btn-primary">
                            ✏️ Grade Students
                        </a>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="table-container">
                    <h2>Student List (<?= $students->num_rows ?> students)</h2>

                    <?php if ($students && $students->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Student No.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Current Grade</th>
                                    <th>Status</th>
                                    <th>Enrolled Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $students->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['student_no']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['program_code'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($row['year_level'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php if (!empty($row['letter_grade'])): ?>
                                                <strong style="font-size: 1.1rem; color: #7B1113;">
                                                    <?= htmlspecialchars($row['letter_grade']) ?>
                                                </strong>
                                            <?php else: ?>
                                                <span style="color: #999;">No grade yet</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $row['status'] === 'Regular' ? 'bg-success' : 'bg-warning' ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($row['date_enrolled'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p style="font-size: 3rem; margin: 0;">👥</p>
                            <h3 style="color: #666; margin: 20px 0 10px;">No Students Enrolled</h3>
                            <p style="color: #999;">There are no students enrolled in this section yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No Sections -->
            <div class="empty-state">
                <p style="font-size: 4rem; margin: 0;">📚</p>
                <h2 style="color: #666; margin: 20px 0 10px;">No Sections Assigned</h2>
                <p style="color: #999;">You don't have any sections assigned yet.<br>Please contact the administrator.</p>
                <a href="dashboard.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">
                    ← Back to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>