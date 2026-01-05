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
    <title>Instructor Management</title>
    <!-- Bootstrap & jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
    <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">

    <style>
        .sortable {
            cursor: pointer;
            position: relative;
            user-select: none;
        }

        .sortable::after {
            content: ' ⇅';
            font-size: 12px;
        }
        .sortable.asc::after {
            content: ' ▲';
        }
        .sortable.desc::after {
            content: ' ▼';
        }
    </style>
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <?php include_once '../../templates/sidebar.php'; ?>

    <div class="content-wrapper">
      <div class="content-container p-4">
        <header class="content-header">
          <h1 class="page-title">Instructor Management</h1>
        </header>

        <div class="action-bar d-flex justify-content-between align-items-center my-4">
          <div class="search-section">
            <form class="search-form d-flex align-items-center" method="GET">
              <input type="text" name="search" class="form-control me-2" placeholder="Search instructor...">
              <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
          </div>
          <div class="button-group d-flex align-items-center">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#instructorAddModal">
              Add Instructor
            </button>
            <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
            <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
          </div>
        </div>

        <div class="table-section">
          <table class="instructor-table">
            <thead>
              <tr>
                <th class="sortable" data-column="0">Last Name</th>
                <th class="sortable" data-column="1">First Name</th>
                <th class="sortable" data-column="2">Email</th>
                <th class="sortable" data-column="3">Department ID</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php include 'tblinstructor.php'; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Instructor Add Modal -->
    <div class="modal fade" id="instructorAddModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Instructor</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form class="instructor-form" id="instructorAddForm">
              <div class="row mb-3">
                <div class="col">
                  <label>Last Name</label>
                  <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="col">
                  <label>First Name</label>
                  <input type="text" name="first_name" class="form-control" required>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col">
                  <label>Department ID</label>
                  <input type="number" name="dept_id" class="form-control" required>
                </div>
              </div>
              <button type="submit" class="btn btn-success w-100">Save Instructor</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Instructor Edit Modal -->
    <div class="modal fade" id="instructorEditModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Edit Instructor</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form class="instructor-form" id="instructorEditForm">
              <input type="hidden" name="instructor_id" id="edit_instructor_id">
              <div class="row mb-3">
                <div class="col">
                  <label>Last Name</label>
                  <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                </div>
                <div class="col">
                  <label>First Name</label>
                  <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label>Email</label>
                  <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="col">
                  <label>Department ID</label>
                  <input type="number" name="dept_id" id="edit_dept_id" class="form-control" required>
                </div>
              </div>
              <button type="submit" class="btn btn-warning w-100">Update Instructor</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Sorting JavaScript -->
    <script>
    $(document).ready(function() {
        // Table sorting functionality
        $('.sortable').on('click', function() {
            const $table = $(this).closest('table');
            const $tbody = $table.find('tbody');
            const columnIndex = parseInt($(this).data('column'));
            const currentOrder = $(this).hasClass('asc') ? 'asc' : ($(this).hasClass('desc') ? 'desc' : 'none');

            // Remove sorting classes from all headers
            $('.sortable').removeClass('asc desc');

            // Determine new sort order
            let newOrder = 'asc';
            if (currentOrder === 'none' || currentOrder === 'desc') {
                newOrder = 'asc';
                $(this).addClass('asc');
            } else {
                newOrder = 'desc';
                $(this).addClass('desc');
            }

            // Get all rows
            const rows = $tbody.find('tr').toArray();

            // Sort rows
            rows.sort(function(a, b) {
                const aValue = $(a).find('td').eq(columnIndex).text().trim();
                const bValue = $(b).find('td').eq(columnIndex).text().trim();

                const aNum = parseFloat(aValue);
                const bNum = parseFloat(bValue);

                let comparison = 0;

                // If both are valid numbers, compare as numbers
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    comparison = aNum - bNum;
                } else {
                    // Otherwise compare as strings
                    comparison = aValue.localeCompare(bValue);
                }

                return newOrder === 'asc' ? comparison : -comparison;
            });

            // Clear tbody and append sorted rows
            $tbody.empty().append(rows);
        });
    });
    </script>

    <script src="../../../dbenrollment_app/assets/js/instructor.js"></script>
</body>
</html>