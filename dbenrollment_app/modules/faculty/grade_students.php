<?php
/**
 * FACULTY - GRADE STUDENTS
 * Submit or update student grades
 */
include('../../config/database.php');
session_start();

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty') {
    header("Location: ../../index.php");
    exit();
}

$faculty_id = $_SESSION['reference_id'] ?? null;
$section_id = $_GET['section_id'] ?? null;

$message = "";
$error = "";

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_grades'])) {
    $grades = $_POST['grades'] ?? [];
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($grades as $enrollment_id => $grade) {
        $grade = trim($grade);
        
        // Validate grade (allow empty to skip)
        if (empty($grade)) continue;
        
        // Update grade
        $stmt = $conn->prepare("UPDATE tblenrollment SET letter_grade = ? WHERE enrollment_id = ?");
        $stmt->bind_param("si", $grade, $enrollment_id);
        
        if ($stmt->execute()) {
            $success_count++;
        } else {
            $error_count++;
        }
    }
    
    if ($success_count > 0) {
        $message = "Successfully updated $success_count grade(s)!";
    }
    if ($error_count > 0) {
        $error = "Failed to update $error_count grade(s).";
    }
}

// If no section selected, show section selector
if (!$section_id) {
    $sections_query = $conn->query("
        SELECT 
            s.section_id,
            s.section_code,
            c.course_code,
            c.course_title,
            COUNT(e.enrollment_id) as student_count
        FROM tblsection s
        JOIN tblcourse c ON s.course_id = c.course_id
        LEFT JOIN tblenrollment e ON s.section_id = e.section_id AND e.is_deleted = 0
        WHERE s.instructor_id = '$faculty_id' AND s.is_deleted = 0
        GROUP BY s.section_id
        ORDER BY c.course_code ASC
    ");
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Select Section - Faculty Portal</title>
        <link rel="stylesheet" href="../../assets/css/sidebar.css">
        <link rel="stylesheet" href="../../assets/css/content.css">
        <link rel="stylesheet" href="../../assets/css/faculty_portal.css">
    </head>
    <body>
        <?php include('../../templates/header.php'); ?>
        <?php include('../../templates/faculty_sidebar.php'); ?>
        
        <main class="main">
            <h1>✏️ Grade Students</h1>
            <p style="color: #666; margin-bottom: 30px;">Select a section to grade students</p>
            
            <div class="table-container">
                <?php if ($sections_query && $sections_query->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Section</th>
                                <th>Students</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $sections_query->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($row['course_code']) ?></strong><br>
                                        <small style="color: #666;"><?= htmlspecialchars($row['course_title']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['section_code']) ?></td>
                                    <td><?= $row['student_count'] ?> students</td>
                                    <td>
                                        <a href="grade_students.php?section_id=<?= $row['section_id'] ?>" 
                                           class="btn btn-primary">
                                            Grade Students →
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align:center; padding: 60px 20px;">
                        <p style="font-size: 3rem; margin: 0;">📚</p>
                        <h3 style="color: #666;">No Sections Available</h3>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </body>
    </html>
    <?php
    exit();
}

// Verify section belongs to faculty
$verify = $conn->query("SELECT section_id FROM tblsection WHERE section_id = '$section_id' AND instructor_id = '$faculty_id'");
if ($verify->num_rows === 0) {
    die("Access denied: You don't have permission to grade this section.");
}

// Get section details
$section_query = $conn->query("
    SELECT 
        s.section_code,
        c.course_code,
        c.course_title
    FROM tblsection s
    JOIN tblcourse c ON s.course_id = c.course_id
    WHERE s.section_id = '$section_id'
");
$section = $section_query->fetch_assoc();

// Get students to grade
$students_query = $conn->query("
    SELECT 
        e.enrollment_id,
        e.letter_grade,
        st.student_no,
        st.first_name,
        st.last_name,
        st.email
    FROM tblenrollment e
    JOIN tblstudent st ON e.student_id = st.student_id
    WHERE e.section_id = '$section_id' AND e.is_deleted = 0
    ORDER BY st.last_name ASC, st.first_name ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Students - Faculty Portal</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/faculty_portal.css">
    <style>
        .grade-input {
            width: 100px;
            padding: 8px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
        }
        .grade-input:focus {
            outline: none;
            border-color: #7B1113;
        }
        .quick-grade-btns {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }
        .quick-grade-btns button {
            padding: 3px 8px;
            font-size: 0.8rem;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            background: #f0f0f0;
            color: #333;
        }
        .quick-grade-btns button:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/faculty_sidebar.php'); ?>

    <main class="main">
        <a href="grade_students.php" style="display: inline-block; margin-bottom: 20px; color: #7B1113; text-decoration: none; font-weight: 600;">
            ← Back to Section Selection
        </a>
        
        <h1>✏️ Grade Students</h1>
        <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <h2 style="color: #7B1113; margin: 0 0 10px 0;">
                <?= htmlspecialchars($section['course_code']) ?> - <?= htmlspecialchars($section['section_code']) ?>
            </h2>
            <p style="color: #666; margin: 0;">
                <?= htmlspecialchars($section['course_title']) ?>
            </p>
        </div>

        <?php if (!empty($message)): ?>
            <div style="padding: 15px; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #10b981;">
                ✓ <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div style="padding: 15px; background: #fee2e2; color: #b91c1c; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ef4444;">
                ✗ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <?php if ($students_query && $students_query->num_rows > 0): ?>
                <form method="POST">
                    <input type="hidden" name="section_id" value="<?= $section_id ?>">
                    
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
                        <strong>Grading Scale:</strong> A, B, C, D, F or 1.0, 1.25, 1.5, 1.75, 2.0, 2.25, 2.5, 2.75, 3.0, 5.0
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Student No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Current Grade</th>
                                <th>Enter/Update Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $students_query->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['student_no']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <?php if (!empty($row['letter_grade'])): ?>
                                            <strong style="font-size: 1.2rem; color: #7B1113;">
                                                <?= htmlspecialchars($row['letter_grade']) ?>
                                            </strong>
                                        <?php else: ?>
                                            <span style="color: #999;">No grade</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="grades[<?= $row['enrollment_id'] ?>]" 
                                            class="grade-input"
                                            value="<?= htmlspecialchars($row['letter_grade'] ?? '') ?>"
                                            placeholder="A, B, C..."
                                            maxlength="5"
                                        >
                                        <div class="quick-grade-btns">
                                            <button type="button" onclick="this.parentElement.previousElementSibling.value='A'">A</button>
                                            <button type="button" onclick="this.parentElement.previousElementSibling.value='B'">B</button>
                                            <button type="button" onclick="this.parentElement.previousElementSibling.value='C'">C</button>
                                            <button type="button" onclick="this.parentElement.previousElementSibling.value='D'">D</button>
                                            <button type="button" onclick="this.parentElement.previousElementSibling.value='F'">F</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 30px; text-align: center;">
                        <button 
                            type="submit" 
                            name="submit_grades" 
                            class="btn btn-primary"
                            style="padding: 15px 40px; font-size: 1.1rem; cursor: pointer;"
                            onclick="return confirm('Are you sure you want to submit these grades?')"
                        >
                            💾 Submit Grades
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div style="text-align:center; padding: 60px 20px;">
                    <p style="font-size: 3rem; margin: 0;">👥</p>
                    <h3 style="color: #666;">No Students to Grade</h3>
                    <p style="color: #999;">There are no students enrolled in this section.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>