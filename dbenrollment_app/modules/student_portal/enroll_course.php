<?php
include('../../config/database.php');
session_start();

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../login.php");
    exit();
}

// Check both common session names and cast to integer for safety
$student_id = $_SESSION['student_id'] ?? $_SESSION['user_id'] ?? null;

$message = "";
$error = "";

// Security Fallback: If no ID is found, stop the script before the database crashes
if (!$student_id) {
    die("Error: Session expired. Please log in again to enroll.");
}

// --- HANDLE ENROLLMENT LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_now'])) {
    $section_id = $_POST['section_id'];

    // 1. Check if student is already enrolled in this specific section
    $check_dup = $conn->prepare("SELECT enrollment_id FROM tblenrollment WHERE student_id = ? AND section_id = ?");
    $check_dup->bind_param("ii", $student_id, $section_id);
    $check_dup->execute();
    if ($check_dup->get_result()->num_rows > 0) {
        $error = "You are already enrolled in this section.";
    } else {
        // 2. Identify the Course ID for this section
        $course_q = $conn->query("SELECT course_id FROM tblsection WHERE section_id = '$section_id'");
        $course_id = $course_q->fetch_assoc()['course_id'];

        // 3. Check for Prerequisite Requirement
        $prereq_q = $conn->query("SELECT prerequisite_course_id FROM tblcourse_prerequisite WHERE course_id = '$course_id'");

        $can_proceed = true;
        if ($prereq_q->num_rows > 0) {
            $required_id = $prereq_q->fetch_assoc()['prerequisite_course_id'];

            // 4. Validate if student passed the prerequisite (A, B, or C)
            $val_sql = "SELECT e.letter_grade FROM tblenrollment e
                        JOIN tblsection s ON e.section_id = s.section_id
                        WHERE e.student_id = ? AND s.course_id = ? AND e.letter_grade IN ('A', 'B', 'C')";
            $val_stmt = $conn->prepare($val_sql);
            $val_stmt->bind_param("ii", $student_id, $required_id);
            $val_stmt->execute();

            if ($val_stmt->get_result()->num_rows == 0) {
                $can_proceed = false;
                $error = "Enrollment Denied: You must pass the prerequisite course first.";
            }
        }

        // 5. Finalize Enrollment
        if ($can_proceed) {
            $enroll_stmt = $conn->prepare("INSERT INTO tblenrollment (student_id, section_id, status, date_enrolled) VALUES (?, ?, 'Regular', NOW())");
            $enroll_stmt->bind_param("ii", $student_id, $section_id);
            if ($enroll_stmt->execute()) {
                $message = "Successfully enrolled!";
            } else {
                $error = "System error during enrollment.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll Course - Student Portal</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/student_portal.css">
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include('../../templates/student_sidebar.php'); ?>

    <main class="main">
        <h1>📝 Course Enrollment</h1>
        <p>Select a section to enroll in the current term.</p>

        <?php if($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Prerequisite</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch sections and their prerequisite names
                    $sql = "SELECT s.section_id, s.section_code, c.course_code,
                            (SELECT cp.course_code FROM tblcourse cp
                             JOIN tblcourse_prerequisite pr ON cp.course_id = pr.prerequisite_course_id
                             WHERE pr.course_id = c.course_id LIMIT 1) as prereq_name
                            FROM tblsection s
                            JOIN tblcourse c ON s.course_id = c.course_id";
                    $result = $conn->query($sql);

                    while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['course_code'] ?></td>
                            <td><?= $row['section_code'] ?></td>
                            <td><?= $row['prereq_name'] ?: '<span style="color:gray">None</span>' ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="section_id" value="<?= $row['section_id'] ?>">
                                    <button type="submit" name="enroll_now" class="btn btn-primary"
                                            onclick="return confirm('Are you sure you want to enroll?')">
                                        Enroll Now
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>