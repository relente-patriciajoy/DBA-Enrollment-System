<?php include_once '../../config/database.php'; ?>
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
                    <form class="enrollment-form" id="enrollmentAddForm">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Student</label>
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

                        <!-- Available Courses Section -->
                        <div id="available_courses_section" style="display:none;">
                            <h6 class="mb-3">Available Courses for Enrollment:</h6>
                            <div id="available_courses_list" class="mb-3">
                                <!-- Courses will be loaded here via AJAX -->
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label>Select Section</label>
                                    <select name="section_id" id="section_select" class="form-select" required>
                                        <option value="">First select a course</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Date Enrolled</label>
                                    <input type="date" name="date_enrolled" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="Regular">Regular</option>
                                        <option value="Irregular">Irregular</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Letter Grade (Optional)</label>
                                <select name="letter_grade" class="form-select">
                                    <option value="">Not yet graded</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="F">F</option>
                                    <option value="INC">INC</option>
                                    <option value="DRP">DRP</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Enroll Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Edit Modal -->
    <div class="modal fade" id="enrollmentEditModal" tabindex="-1" aria-labelledby="enrollmentEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Enrollment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="enrollment-form" id="enrollmentEditForm">
                        <input type="hidden" name="enrollment_id" id="edit_enrollment_id">
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label>Student ID</label>
                                <select name="student_id" id="edit_student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                    <?php
                                    $studentQuery2 = $conn->query("SELECT student_id, CONCAT(last_name, ', ', first_name) as full_name FROM tblstudent WHERE is_deleted = 0 ORDER BY last_name ASC");
                                    while($student = $studentQuery2->fetch_assoc()) {
                                        echo "<option value='{$student['student_id']}'>{$student['full_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label>Section ID</label>
                                <input type="number" name="section_id" id="edit_section_id" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Date Enrolled</label>
                                <input type="date" name="date_enrolled" id="edit_date_enrolled" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Status</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="Regular">Regular</option>
                                    <option value="Irregular">Irregular</option>
                                    <option value="Dropped">Dropped</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Letter Grade</label>
                            <select name="letter_grade" id="edit_letter_grade" class="form-select">
                                <option value="">Select Grade (Optional)</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="F">F</option>
                                <option value="INC">INC</option>
                                <option value="DRP">DRP</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Enrollment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../dbenrollment_app/assets/js/enrollment.js"></script>
</body>
</html>