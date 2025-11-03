<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Department Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include_once '../../templates/sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-container p-4">
            <header class="content-header">
                <h1 class="page-title">Department Management</h1>
            </header>

            <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search department...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentAddModal">
                        Add Department
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div>

            <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Department ID</th>
                            <th class="student-table__header">Department Code</th>
                            <th class="student-table__header">Description</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tbldepartment.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Department Add Modal -->
    <div class="modal fade" id="departmentAddModal" tabindex="-1" aria-labelledby="departmentAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="department-form" id="departmentAddForm">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Department Code</label>
                                <input type="text" name="dept_code" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Department Name</label>
                                <input type="text" name="dept_name" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Save Department</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Edit Modal -->
    <div class="modal fade" id="departmentEditModal" tabindex="-1" aria-labelledby="departmentEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="department-form" id="departmentEditForm">
                        <input type="hidden" name="dept_id" id="edit_dept_id">

                        <div class="row mb-3">
                            <div class="col">
                                <label>Department Code</label>
                                <input type="text" name="dept_code" id="edit_dept_code" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Department Name</label>
                                <input type="text" name="dept_name" id="edit_dept_name" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Department</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Move inline scripts to external file -->
    <script src="../../../dbenrollment_app/assets/js/department.js"></script>
</body>
</html>