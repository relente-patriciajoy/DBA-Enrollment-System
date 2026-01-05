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
  <title>Program Management</title>

  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom Styles -->
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">
</head>
<body>
  <?php include('../../templates/header.php'); ?>
  <?php include_once '../../templates/sidebar.php'; ?>

  <div class="content-wrapper">
      <div class="content-container p-4">
          <header class="content-header">
              <h1 class="page-title">Program Management</h1>
          </header>

          <!-- Search + Action Buttons -->
          <div class="action-bar d-flex justify-content-between align-items-center my-4">
              <div class="search-section">
                  <form class="search-form d-flex align-items-center" method="GET">
                      <input type="text" name="search" class="form-control me-2" placeholder="Search program...">
                      <button type="submit" class="btn btn-outline-primary">Search</button>
                  </form>
              </div>
              <div class="button-group d-flex align-items-center">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                      Add Program
                  </button>
                  <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                  <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
              </div>
          </div>

          <!-- Table Section -->
          <div class="table-section">
              <table class="student-table">
                  <thead>
                      <tr>
                          <th class="student-table__header">Program Code</th>
                          <th class="student-table__header">Program Name</th>
                          <th class="student-table__header">Department</th>
                          <th class="student-table__header">Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php include 'tblprogram.php'; ?>
                  </tbody>
              </table>
          </div>
      </div>
  </div>

  <!-- Add Program Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addModalLabel">Add Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addProgramForm">
            <div class="row mb-3">
              <div class="col">
                <label>Program Code</label>
                <input type="text" name="program_code" class="form-control" required>
              </div>
              <div class="col">
                <label>Program Name</label>
                <input type="text" name="program_name" class="form-control" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label>Department</label>
                <select name="dept_id" class="form-select" required>
                  <?php
                    $deptRes = $conn->query("SELECT dept_id, dept_name FROM tbldepartment WHERE is_deleted = 0 ORDER BY dept_name ASC");
                    while ($dept = $deptRes->fetch_assoc()) {
                        echo "<option value='{$dept['dept_id']}'>{$dept['dept_name']}</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-success w-100">Save Program</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Program Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editProgramForm">
            <input type="hidden" name="program_id" id="edit_program_id">
            <div class="row mb-3">
              <div class="col">
                <label>Program Code</label>
                <input type="text" name="program_code" id="edit_program_code" class="form-control" required>
              </div>
              <div class="col">
                <label>Program Name</label>
                <input type="text" name="program_name" id="edit_program_name" class="form-control" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label>Department</label>
                <select name="dept_id" id="edit_dept_id" class="form-select" required>
                  <?php
                    $deptRes = $conn->query("SELECT dept_id, dept_name FROM tbldepartment WHERE is_deleted = 0 ORDER BY dept_name ASC");
                    while ($dept = $deptRes->fetch_assoc()) {
                        echo "<option value='{$dept['dept_id']}'>{$dept['dept_name']}</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-warning w-100">Update Program</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="../../../dbenrollment_app/assets/js/program.js"></script>
</body>
</html>