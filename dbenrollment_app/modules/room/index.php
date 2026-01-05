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
  <title>Room Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/sidebar.css">
  <link rel="stylesheet" href="../../../dbenrollment_app/assets/css/content.css">

  <style>
    .new-room-highlight {
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
                <h1 class="page-title">Room Management</h1>
            </header>

            <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search room...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roomAddModal">
                        Add Room
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div>

            <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Room ID</th>
                            <th class="student-table__header">Room Code</th>
                            <th class="student-table__header">Building</th>
                            <th class="student-table__header">Capacity</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tblroom.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Room Add Modal -->
    <div class="modal fade" id="roomAddModal" tabindex="-1" aria-labelledby="roomAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="room-form" id="roomAddForm">
                        <div class="mb-3">
                            <label>Room Code</label>
                            <input type="text" name="room_code" class="form-control" required placeholder="e.g., A101">
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Building</label>
                                <input type="text" name="building" class="form-control" required placeholder="e.g., Main Building">
                            </div>
                            <div class="col">
                                <label>Capacity</label>
                                <input type="number" name="capacity" class="form-control" required min="1" placeholder="e.g., 40">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Save Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Edit Modal -->
    <div class="modal fade" id="roomEditModal" tabindex="-1" aria-labelledby="roomEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="room-form" id="roomEditForm">
                        <input type="hidden" name="room_id" id="edit_room_id">

                        <div class="mb-3">
                            <label>Room Code</label>
                            <input type="text" name="room_code" id="edit_room_code" class="form-control" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Building</label>
                                <input type="text" name="building" id="edit_building" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Capacity</label>
                                <input type="number" name="capacity" id="edit_capacity" class="form-control" required min="1">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../dbenrollment_app/assets/js/room.js"></script>
</body>
</html>