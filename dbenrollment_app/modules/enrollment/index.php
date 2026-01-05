<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Enrollment Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">

  <style>
    .new-enrollment-highlight {
    background-color: #d4edda !important;
    animation: fadeHighlight 2s ease-in-out;
        }

        @keyframes fadeHighlight {
            0% { background-color: #d4edda; }
            100% { background-color: transparent; }
        }

        /* Ensure proper modal stacking */
        .modal-backdrop {
            z-index: 1040;
        }

        .modal {
            z-index: 1050;
        }

        /* Ensure modal is positioned correctly */
        .modal.show {
            display: block !important;
        }

        .modal-dialog {
            margin: 1.75rem auto;
        }

        /* Remove any transforms that might hide content */
        .modal.show .modal-dialog {
            transform: none;
        }

  </style>
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include_once '../../templates/sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-container p-4">
            <header class="content-header">
                <h1 class="page-title">Enrollment Management</h1>
            </header>

            <!-- Add Tabs for Regular/Irregular -->
            <ul class="nav nav-tabs mb-3" id="enrollmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-enrollments" type="button">
                        All Enrollments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="regular-tab" data-bs-toggle="tab" data-bs-target="#regular-enrollments" type="button">
                        Regular Students
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="irregular-tab" data-bs-toggle="tab" data-bs-target="#irregular-enrollments" type="button">
                        Irregular Students <span class="badge bg-warning">!</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="enrollmentTabContent">
                <!-- All Enrollments Tab -->
                <div class="tab-pane fade show active" id="all-enrollments" role="tabpanel">
                    <div class="action-bar d-flex justify-content-between align-items-center my-4">
                        <div class="search-section">
                            <form class="search-form d-flex align-items-center" method="GET">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search by student or section ID...">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="button-group d-flex align-items-center">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollmentAddModal">
                                Add Enrollment
                            </button>
                            <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                            <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                        </div>
                    </div>

                    <div class="table-section">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th class="student-table__header">Enrollment ID</th>
                                    <th class="student-table__header">Student ID</th>
                                    <th class="student-table__header">Section ID</th>
                                    <th class="student-table__header">Date Enrolled</th>
                                    <th class="student-table__header">Status</th>
                                    <th class="student-table__header">Letter Grade</th>
                                    <th class="student-table__header">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php include 'tblenrollment.php'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Regular Students Tab -->
                <div class="tab-pane fade" id="regular-enrollments" role="tabpanel">
                    <div class="alert alert-success mb-3">
                        <strong>Regular Students:</strong> Students with NO failed courses - all in good academic standing
                    </div>

                    <div class="table-section">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Student No</th>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Total Enrollments</th>
                                    <th>Passed Courses</th>
                                    <th>Pending Grades</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query to get STUDENTS who are regular (no failed courses)
                                $sql = "SELECT
                                            s.student_id,
                                            s.student_no,
                                            CONCAT(s.first_name, ' ', s.last_name) as student_name,
                                            p.program_name,
                                            s.year_level,
                                            COUNT(e.enrollment_id) as total_enrollments,
                                            COUNT(CASE WHEN e.letter_grade IN ('A', 'B', 'C') THEN 1 END) as passed_count,
                                            COUNT(CASE WHEN e.letter_grade IN ('D', 'F', 'INC') THEN 1 END) as failed_count,
                                            COUNT(CASE WHEN e.letter_grade IS NULL OR e.letter_grade = '' THEN 1 END) as pending_count
                                        FROM tblstudent s
                                        INNER JOIN tblprogram p ON s.program_id = p.program_id
                                        LEFT JOIN tblenrollment e ON s.student_id = e.student_id AND e.is_deleted = 0
                                        LEFT JOIN tblsection sec ON e.section_id = sec.section_id AND sec.is_deleted = 0
                                        WHERE s.is_deleted = 0
                                        GROUP BY s.student_id, s.student_no, student_name, p.program_name, s.year_level
                                        HAVING failed_count = 0 AND total_enrollments > 0
                                        ORDER BY s.student_no ASC";

                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='table-success'>";
                                        echo "<td><strong>" . htmlspecialchars($row['student_no']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['program_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['year_level']) . "</td>";
                                        echo "<td class='text-center'>{$row['total_enrollments']}</td>";
                                        echo "<td class='text-center'><span class='badge bg-success'>{$row['passed_count']}</span></td>";
                                        echo "<td class='text-center'><span class='badge bg-secondary'>{$row['pending_count']}</span></td>";
                                        echo "<td>
                                            <a href='?search={$row['student_id']}&tab=all' class='btn btn-info btn-sm'>
                                                View Enrollments
                                            </a>
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

                <!-- Irregular Students Tab -->
                <div class="tab-pane fade" id="irregular-enrollments" role="tabpanel">
                    <div class="alert alert-warning mb-3">
                        <strong>Irregular Students:</strong> Students with at least one failed course (D, F, or INC grade)
                    </div>

                    <div class="table-section">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Student No</th>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Total Enrollments</th>
                                    <th>Passed (A,B,C)</th>
                                    <th>Failed (D,F,INC)</th>
                                    <th>Failed Courses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query to get STUDENTS (not enrollments) who are irregular
                                $sql = "SELECT
                                            s.student_id,
                                            s.student_no,
                                            CONCAT(s.first_name, ' ', s.last_name) as student_name,
                                            p.program_name,
                                            s.year_level,
                                            COUNT(e.enrollment_id) as total_enrollments,
                                            COUNT(CASE WHEN e.letter_grade IN ('A', 'B', 'C') THEN 1 END) as passed_count,
                                            COUNT(CASE WHEN e.letter_grade IN ('D', 'F', 'INC') THEN 1 END) as failed_count,
                                            GROUP_CONCAT(
                                                DISTINCT CASE WHEN e.letter_grade IN ('D', 'F', 'INC')
                                                THEN CONCAT(c.course_code, ' (', e.letter_grade, ')')
                                                END
                                                ORDER BY c.course_code
                                                SEPARATOR ', '
                                            ) as failed_courses
                                        FROM tblstudent s
                                        INNER JOIN tblprogram p ON s.program_id = p.program_id
                                        INNER JOIN tblenrollment e ON s.student_id = e.student_id
                                        INNER JOIN tblsection sec ON e.section_id = sec.section_id
                                        INNER JOIN tblcourse c ON sec.course_id = c.course_id
                                        WHERE s.is_deleted = 0
                                        AND e.is_deleted = 0
                                        GROUP BY s.student_id, s.student_no, student_name, p.program_name, s.year_level
                                        HAVING failed_count > 0
                                        ORDER BY failed_count DESC, s.student_no ASC";

                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='table-warning'>";
                                        echo "<td><strong>" . htmlspecialchars($row['student_no']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['program_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['year_level']) . "</td>";
                                        echo "<td class='text-center'>{$row['total_enrollments']}</td>";
                                        echo "<td class='text-center'><span class='badge bg-success'>{$row['passed_count']}</span></td>";
                                        echo "<td class='text-center'><span class='badge bg-danger'>{$row['failed_count']}</span></td>";
                                        echo "<td><small>" . htmlspecialchars($row['failed_courses']) . "</small></td>";
                                        echo "<td>
                                            <a href='?search={$row['student_id']}&tab=all' class='btn btn-primary btn-sm'>
                                                View Enrollments
                                            </a>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9' class='text-center text-success'><strong>✓ No irregular students found - All students in good standing!</strong></td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by student or section ID...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollmentAddModal">
                        Add Enrollment
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div> -->

            <!-- <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Enrollment ID</th>
                            <th class="student-table__header">Student ID</th>
                            <th class="student-table__header">Section ID</th>
                            <th class="student-table__header">Date Enrolled</th>
                            <th class="student-table__header">Status</th>
                            <th class="student-table__header">Letter Grade</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tblenrollment.php'; ?>
                    </tbody>
                </table>
            </div> -->
        </div>
    </div>

    <!-- Enrollment Add Modal -->
    <div class="modal fade" id="enrollmentAddModal" tabindex="-1" aria-labelledby="enrollmentAddModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Enroll Student in Course</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Student Selection -->
            <div class="row mb-3">
              <div class="col">
                <label class="form-label">Student</label>
                <select name="student_id" id="student_select" class="form-select" required>
                  <option value="">Select Student</option>
                  <?php
                  $studentQuery = $conn->query("SELECT student_id, student_no, CONCAT(last_name, ', ', first_name) as full_name FROM tblstudent WHERE is_deleted = 0 ORDER BY last_name ASC");
                  while($student = $studentQuery->fetch_assoc()) {
                      echo "<option value='{$student['student_id']}'>{$student['student_no']} - {$student['full_name']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- Available Courses Section (loaded dynamically) -->
            <div id="available_courses_section" style="display:none;">
              <div id="available_courses_list">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Edit Enrollment Modal -->
    <div class="modal fade" id="enrollmentEditModal" tabindex="-1" aria-labelledby="enrollmentEditModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="enrollmentEditModalLabel">Edit Enrollment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="enrollmentEditForm">
            <div class="modal-body">
              <input type="hidden" id="edit_enrollment_id" name="enrollment_id">

              <div class="mb-3">
                <label class="form-label">Student</label>
                <input type="text" id="edit_student_name" class="form-control" disabled>
              </div>

              <div class="mb-3">
                <label class="form-label">Course - Section</label>
                <input type="text" id="edit_course_info" class="form-control" disabled>
              </div>

              <div class="mb-3">
                <label class="form-label">Date Enrolled</label>
                <input type="date" id="edit_date_enrolled" name="date_enrolled" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Status</label>
                <select id="edit_status" name="status" class="form-select" required>
                  <option value="Regular">Regular</option>
                  <option value="Irregular">Irregular</option>
                  <option value="Dropped">Dropped</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label"><strong>Letter Grade</strong> (Leave empty if not yet graded)</label>
                <select id="edit_letter_grade" name="letter_grade" class="form-select">
                  <option value="">Not yet graded</option>
                  <option value="A">A (Excellent - Passed)</option>
                  <option value="B">B (Very Good - Passed)</option>
                  <option value="C">C (Good - Passed)</option>
                  <option value="D">D (Fair - Failed)</option>
                  <option value="F">F (Failed)</option>
                  <option value="INC">INC (Incomplete)</option>
                  <option value="W">W (Withdrawn)</option>
                </select>
                <div class="form-text">
                  Note: Only A, B, C grades count as passing for prerequisites
                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn_update_enrollment">Update Enrollment</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="../../../dbenrollment_app/assets/js/enrollment.js"></script>
    <script>
        $(document).ready(function() {
            console.log('Index ready. Handlers loaded from enrollment.js');

            // Cleanup stray backdrops on modal close
            $('.modal').on('hidden.bs.modal', function () {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });
        });
    </script>
</body>
</html>