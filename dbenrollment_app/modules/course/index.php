<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Course Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">

  <style>
    .new-course-highlight {
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
                <h1 class="page-title">Course Management</h1>
            </header>

            <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search course...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#courseAddModal">
                        Add Course
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div>

            <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Course ID</th>
                            <th class="student-table__header">Course Code</th>
                            <th class="student-table__header">Course Title</th>
                            <th class="student-table__header">Units</th>
                            <th class="student-table__header">Lecture Hours</th>
                            <th class="student-table__header">Lab Hours</th>
                            <th class="student-table__header">Department ID</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tblcourse.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Course Add Modal -->
    <div class="modal fade" id="courseAddModal" tabindex="-1" aria-labelledby="courseAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="course-form" id="courseAddForm">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Course Code</label>
                                <input type="text" name="course_code" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Course Title</label>
                                <input type="text" name="course_title" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Units</label>
                                <input type="number" name="units" class="form-control" required min="0" step="0.5">
                            </div>
                            <div class="col">
                                <label>Lecture Hours</label>
                                <input type="number" name="lecture_hours" class="form-control" required min="0">
                            </div>
                            <div class="col">
                                <label>Lab Hours</label>
                                <input type="number" name="lab_hours" class="form-control" required min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Department</label>
                            <select name="dept_id" class="form-select" required>
                                <option value="">Select Department</option>
                                <?php
                                $deptQuery = $conn->query("SELECT dept_id, dept_name FROM tbldepartment WHERE is_deleted = 0 ORDER BY dept_name ASC");
                                while($dept = $deptQuery->fetch_assoc()) {
                                    echo "<option value='{$dept['dept_id']}'>{$dept['dept_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Save Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Edit Modal -->
    <div class="modal fade" id="courseEditModal" tabindex="-1" aria-labelledby="courseEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="course-form" id="courseEditForm">
                        <input type="hidden" name="course_id" id="edit_course_id">

                        <div class="row mb-3">
                            <div class="col">
                                <label>Course Code</label>
                                <input type="text" name="course_code" id="edit_course_code" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Course Title</label>
                                <input type="text" name="course_title" id="edit_course_title" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Units</label>
                                <input type="number" name="units" id="edit_units" class="form-control" required min="0" step="0.5">
                            </div>
                            <div class="col">
                                <label>Lecture Hours</label>
                                <input type="number" name="lecture_hours" id="edit_lecture_hours" class="form-control" required min="0">
                            </div>
                            <div class="col">
                                <label>Lab Hours</label>
                                <input type="number" name="lab_hours" id="edit_lab_hours" class="form-control" required min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Department</label>
                            <select name="dept_id" id="edit_dept_id" class="form-select" required>
                                <option value="">Select Department</option>
                                <?php
                                $deptQuery2 = $conn->query("SELECT dept_id, dept_name FROM tbldepartment WHERE is_deleted = 0 ORDER BY dept_name ASC");
                                while($dept = $deptQuery2->fetch_assoc()) {
                                    echo "<option value='{$dept['dept_id']}'>{$dept['dept_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../dbenrollment_app/assets/js/course.js"></script>
</body>
</html>