<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Term Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">

  <style>
    .new-term-highlight {
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
                <h1 class="page-title">Term Management</h1>
            </header>

            <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search term...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#termAddModal">
                        Add Term
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div>

            <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Term ID</th>
                            <th class="student-table__header">Term Code</th>
                            <th class="student-table__header">Start Date</th>
                            <th class="student-table__header">End Date</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tblterm.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Term Add Modal -->
    <div class="modal fade" id="termAddModal" tabindex="-1" aria-labelledby="termAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Term</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="term-form" id="termAddForm">
                        <div class="mb-3">
                            <label>Term Code</label>
                            <input type="text" name="term_code" class="form-control" required placeholder="e.g., 2024-2025-1ST">
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Save Term</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Term Edit Modal -->
    <div class="modal fade" id="termEditModal" tabindex="-1" aria-labelledby="termEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Term</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="term-form" id="termEditForm">
                        <input type="hidden" name="term_id" id="edit_term_id">

                        <div class="mb-3">
                            <label>Term Code</label>
                            <input type="text" name="term_code" id="edit_term_code" class="form-control" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Term</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../dbenrollment_app/assets/js/term.js"></script>
</body>
</html>