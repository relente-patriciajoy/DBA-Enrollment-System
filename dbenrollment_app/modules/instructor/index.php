<?php include_once '../../config/database.php'; ?>
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
</head>
<body>
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
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Email</th>
                            <th>Department ID</th>
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

    <script>
    $(document).ready(function() {
        $('#instructorAddForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'add_ajax.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#instructorAddModal').modal('hide');
                        $('#instructorAddForm')[0].reset();
                        alert('Instructor added successfully!');
                        location.reload();
                    } else {
                        alert(response.error || 'Failed to add instructor');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error adding instructor. Please try again.');
                }
            });
        });

        $('#instructorEditForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'update_ajax.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#instructorEditModal').modal('hide');
                        alert('Instructor updated successfully!');
                        location.reload();
                    } else {
                        alert(response.error || 'Failed to update instructor');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error updating instructor. Please try again.');
                }
            });
        });
    });

    function editInstructor(id) {
        $.ajax({
            url: 'get_instructor.php',
            type: 'GET',
            data: { instructor_id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var instructor = response.data;
                    $('#edit_instructor_id').val(instructor.instructor_id);
                    $('#edit_last_name').val(instructor.last_name);
                    $('#edit_first_name').val(instructor.first_name);
                    $('#edit_email').val(instructor.email);
                    $('#edit_dept_id').val(instructor.dept_id);
                    $('#instructorEditModal').modal('show');
                } else {
                    alert(response.error || 'Failed to load instructor data');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error loading instructor data');
            }
        });
    }

    function deleteInstructor(id) {
        if (confirm('Are you sure you want to delete this instructor?')) {
            $.ajax({
                url: 'delete_ajax.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    location.reload();
                }
            });
        }
    }
    </script>
</body>
</html>