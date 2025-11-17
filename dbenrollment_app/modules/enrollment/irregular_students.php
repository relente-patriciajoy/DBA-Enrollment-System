<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Irregular Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
    <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include_once '../../templates/sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-container p-4">
            <header class="content-header">
                <h1 class="page-title">
                    <i class="bi bi-exclamation-triangle-fill text-warning"></i> Irregular Students
                </h1>
            </header>

            <div class="alert alert-warning mb-4">
                <strong>Irregular Students:</strong> Students who have <strong>failed courses</strong> (grades D, F, or INC)
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="studentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="irregular-tab" data-bs-toggle="tab" data-bs-target="#irregular" type="button">
                        Irregular Students
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="regular-tab" data-bs-toggle="tab" data-bs-target="#regular" type="button">
                        Regular Students
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="studentTabContent">
                <!-- Irregular Students Tab -->
                <div class="tab-pane fade show active" id="irregular" role="tabpanel">
                    <div class="table-section">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Student No</th>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Status</th>
                                    <th>Failed Courses</th>
                                    <th>Ungraded Courses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // IRREGULAR STUDENTS QUERY
                                $sql = "SELECT DISTINCT
                                            s.student_id,
                                            s.student_no,
                                            CONCAT(s.first_name, ' ', s.last_name) as student_name,
                                            p.program_name,
                                            s.year_level,
                                            COUNT(CASE WHEN e.letter_grade IN ('D', 'F', 'INC') THEN 1 END) as failed_count,
                                            GROUP_CONCAT(
                                                DISTINCT CASE WHEN e.letter_grade IN ('D', 'F', 'INC')
                                                THEN CONCAT(c.course_code, ' (', e.letter_grade, ')')
                                                END SEPARATOR ', '
                                            ) as failed_courses,
                                            GROUP_CONCAT(
                                                DISTINCT CASE WHEN e.letter_grade IS NULL OR e.letter_grade = ''
                                                THEN c.course_code
                                                END SEPARATOR ', '
                                            ) as ungraded_courses
                                        FROM tblstudent s
                                        INNER JOIN tblprogram p ON s.program_id = p.program_id
                                        INNER JOIN tblenrollment e ON s.student_id = e.student_id
                                        INNER JOIN tblsection sec ON e.section_id = sec.section_id
                                        INNER JOIN tblcourse c ON sec.course_id = c.course_id
                                        WHERE s.is_deleted = 0
                                        AND e.is_deleted = 0
                                        GROUP BY s.student_id
                                        HAVING failed_count > 0
                                        ORDER BY failed_count DESC, s.student_no ASC";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='table-warning'>";
                                        echo "<td>" . htmlspecialchars($row['student_no']) . "</td>";
                                        echo "<td><strong>" . htmlspecialchars($row['student_name']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['program_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['year_level']) . "</td>";
                                        echo "<td><span class='badge bg-danger'>{$row['failed_count']} Failed Course(s)</span></td>";
                                        echo "<td><small>" . htmlspecialchars($row['failed_courses']) . "</small></td>";
                                        echo "<td><small class='text-muted'>" . htmlspecialchars($row['ungraded_courses'] ?: 'All graded') . "</small></td>";
                                        echo "<td>
                                            <button class='btn btn-primary btn-sm' onclick='alert(\"Student details feature coming soon\")'>
                                                View Details
                                            </button>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center text-success'><strong>✓ No irregular students - All students in good standing!</strong></td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Regular Students Tab -->
                <div class="tab-pane fade" id="regular" role="tabpanel">
                    <div class="table-section">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Student No</th>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Status</th>
                                    <th>Passed Courses</th>
                                    <th>Total Enrollments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // REGULAR STUDENTS QUERY
                                $sql = "SELECT DISTINCT
                                            s.student_id,
                                            s.student_no,
                                            CONCAT(s.first_name, ' ', s.last_name) as student_name,
                                            p.program_name,
                                            s.year_level,
                                            COUNT(CASE WHEN e.letter_grade IN ('A', 'B', 'C') THEN 1 END) as passed_count,
                                            COUNT(CASE WHEN e.letter_grade IN ('D', 'F', 'INC') THEN 1 END) as failed_count,
                                            COUNT(e.enrollment_id) as total_enrollments
                                        FROM tblstudent s
                                        INNER JOIN tblprogram p ON s.program_id = p.program_id
                                        LEFT JOIN tblenrollment e ON s.student_id = e.student_id AND e.is_deleted = 0
                                        LEFT JOIN tblsection sec ON e.section_id = sec.section_id
                                        WHERE s.is_deleted = 0
                                        GROUP BY s.student_id
                                        HAVING failed_count = 0
                                        ORDER BY s.student_no ASC";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='table-success'>";
                                        echo "<td>" . htmlspecialchars($row['student_no']) . "</td>";
                                        echo "<td><strong>" . htmlspecialchars($row['student_name']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['program_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['year_level']) . "</td>";
                                        echo "<td><span class='badge bg-success'>Good Standing</span></td>";
                                        echo "<td>{$row['passed_count']}</td>";
                                        echo "<td>{$row['total_enrollments']}</td>";
                                        echo "<td>
                                            <button class='btn btn-info btn-sm' onclick='alert(\"Student details feature coming soon\")'>
                                                View Details
                                            </button>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center'>No regular students found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>