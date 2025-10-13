<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Course Prerequisites Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">

  <style>
    .new-prereq-highlight {
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
    <?php include_once '../../templates/sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-container p-4">
            <header class="content-header">
                <h1 class="page-title">Course Prerequisites Management</h1>
            </header>

            <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search course...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#prereqAddModal">
                        Add Prerequisite
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div>

            <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Prereq ID</th>
                            <th class="student-table__header">Course Code</th>
                            <th class="student-table__header">Course Title</th>
                            <th class="student-table__header">Prerequisite Course Code</th>
                            <th class="student-table__header">Prerequisite Course Title</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tblcourse_prereq.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Prerequisite Add Modal -->
    <div class="modal fade" id="prereqAddModal" tabindex="-1" aria-labelledby="prereqAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Course Prerequisite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="prereq-form" id="prereqAddForm">
                        <div class="mb-3">
                            <label>Course</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">Select Course</option>
                                <?php
                                $courseQuery = $conn->query("SELECT course_id, course_code, course_title FROM tblcourse WHERE is_deleted = 0 ORDER BY course_code ASC");
                                while($course = $courseQuery->fetch_assoc()) {
                                    echo "<option value='{$course['course_id']}'>{$course['course_code']} - {$course['course_title']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Prerequisite Course</label>
                            <select name="prerequisite_course_id" class="form-select" required>
                                <option value="">Select Prerequisite Course</option>
                                <?php
                                $prereqQuery = $conn->query("SELECT course_id, course_code, course_title FROM tblcourse WHERE is_deleted = 0 ORDER BY course_code ASC");
                                while($prereq = $prereqQuery->fetch_assoc()) {
                                    echo "<option value='{$prereq['course_id']}'>{$prereq['course_code']} - {$prereq['course_title']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Save Prerequisite</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Prerequisite Edit Modal -->
    <div class="modal fade" id="prereqEditModal" tabindex="-1" aria-labelledby="prereqEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Course Prerequisite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="prereq-form" id="prereqEditForm">
                        <input type="hidden" name="prereq_id" id="edit_prereq_id">

                        <div class="mb-3">
                            <label>Course</label>
                            <select name="course_id" id="edit_course_id" class="form-select" required>
                                <option value="">Select Course</option>
                                <?php
                                $courseQuery2 = $conn->query("SELECT course_id, course_code, course_title FROM tblcourse WHERE is_deleted = 0 ORDER BY course_code ASC");
                                while($course = $courseQuery2->fetch_assoc()) {
                                    echo "<option value='{$course['course_id']}'>{$course['course_code']} - {$course['course_title']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Prerequisite Course</label>
                            <select name="prerequisite_course_id" id="edit_prerequisite_course_id" class="form-select" required>
                                <option value="">Select Prerequisite Course</option>
                                <?php
                                $prereqQuery2 = $conn->query("SELECT course_id, course_code, course_title FROM tblcourse WHERE is_deleted = 0 ORDER BY course_code ASC");
                                while($prereq = $prereqQuery2->fetch_assoc()) {
                                    echo "<option value='{$prereq['course_id']}'>{$prereq['course_code']} - {$prereq['course_title']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Prerequisite</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../dbenrollment_app/assets/js/course_prereq.js"></script>
</body>
</html>