<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Section Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">

  <style>
    .new-section-highlight {
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
                <h1 class="page-title">Section Management</h1>
            </header>

            <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by section code...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectionAddModal">
                        Add Section
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div>

            <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Section ID</th>
                            <th class="student-table__header">Section Code</th>
                            <th class="student-table__header">Course ID</th>
                            <th class="student-table__header">Term ID</th>
                            <th class="student-table__header">Instructor ID</th>
                            <th class="student-table__header">Day Pattern</th>
                            <th class="student-table__header">Start Time</th>
                            <th class="student-table__header">End Time</th>
                            <th class="student-table__header">Room ID</th>
                            <th class="student-table__header">Max Capacity</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tblsection.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section Add Modal -->
    <div class="modal fade" id="sectionAddModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="section-form" id="sectionAddForm">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>Section Code</label>
                                <input type="text" name="section_code" class="form-control" placeholder="e.g., DIT 3-1" required>
                            </div>
                            <div class="col-md-3">
                                <label>Course ID</label>
                                <input type="number" name="course_id" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Term ID</label>
                                <input type="number" name="term_id" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Year Level</label>
                                <select name="year_level" class="form-select" required>
                                    <option value="">Select Year</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Instructor ID</label>
                                <input type="number" name="instructor_id" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Day Pattern</label>
                                <input type="text" name="day_pattern" class="form-control" placeholder="e.g., MWF, TTH" required>
                            </div>
                            <div class="col-md-4">
                                <label>Room ID</label>
                                <input type="number" name="room_id" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Start Time</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>End Time</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Max Capacity</label>
                                <input type="number" name="max_capacity" class="form-control" required min="1">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Save Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Edit Modal -->
    <div class="modal fade" id="sectionEditModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="section-form" id="sectionEditForm">
                        <input type="hidden" name="section_id" id="edit_section_id">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>Section Code</label>
                                <input type="text" name="section_code" id="edit_section_code" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Course ID</label>
                                <input type="number" name="course_id" id="edit_course_id" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Term ID</label>
                                <input type="number" name="term_id" id="edit_term_id" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Year Level</label>
                                <select name="year_level" id="edit_year_level" class="form-select" required>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Instructor ID</label>
                                <input type="number" name="instructor_id" id="edit_instructor_id" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Day Pattern</label>
                                <input type="text" name="day_pattern" id="edit_day_pattern" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Room ID</label>
                                <input type="number" name="room_id" id="edit_room_id" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Start Time</label>
                                <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>End Time</label>
                                <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Max Capacity</label>
                                <input type="number" name="max_capacity" id="edit_max_capacity" class="form-control" required min="1">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../dbenrollment_app/assets/js/section.js"></script>
</body>
</html>