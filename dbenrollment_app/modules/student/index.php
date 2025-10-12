<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Student Management</title>
  <!-- Bootstrap & jQuery -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../../assets/css/sidebar.css">
  <link rel="stylesheet" href="../../assets/css/content.css">
</head>
<body>
    <?php include_once '../../templates/sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="content-container p-4">
            <header class="content-header">
                <h1 class="page-title">Student Management</h1>
            </header>

            <div class="action-bar d-flex justify-content-between align-items-center my-4">
                <div class="search-section">
                    <form class="search-form d-flex align-items-center" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search student...">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="button-group d-flex align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentAddModal">
                        Add Student
                    </button>
                    <button class="btn btn-success mx-2" onclick="window.location.href='export_excel.php'">Export to Excel</button>
                    <button class="btn btn-danger" onclick="window.location.href='export_pdf.php'">Export to PDF</button>
                </div>
            </div>

            <div class="table-section">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th class="student-table__header">Student No</th>
                            <th class="student-table__header">Last Name</th>
                            <th class="student-table__header">First Name</th>
                            <th class="student-table__header">Email</th>
                            <th class="student-table__header">Gender</th>
                            <th class="student-table__header">Birthdate</th>
                            <th class="student-table__header">Year Level</th>
                            <th class="student-table__header">Program ID</th>
                            <th class="student-table__header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'tblstudent.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Student Add Modal -->
    <div class="modal fade" id="studentAddModal" tabindex="-1" aria-labelledby="studentAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="student-form" id="studentAddForm">
          <div class="row mb-3">
            <div class="col">
              <label>Student No</label>
              <input type="text" name="student_no" class="form-control" required>
            </div>
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
              <label>Gender</label>
              <select name="gender" class="form-select" required>
                  <option value="">Select</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
              </select>
            </div>
            <div class="col">
              <label>Birthdate</label>
              <input type="date" name="birthdate" class="form-control">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col">
              <label>Year Level</label>
              <select name="year_level" class="form-select" required>
                <option value="">Select</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
              </select>
            </div>
            <div class="col">
              <label>Program ID</label>
              <input type="number" name="program_id" class="form-control" required>
            </div>
          </div>

          <button type="submit" class="btn btn-success w-100">Save Student</button>
        </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Edit Modal -->
    <div class="modal fade" id="studentEditModal" tabindex="-1" aria-labelledby="studentEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="student-form" id="studentEditForm">
          <input type="hidden" name="student_id" id="edit_student_id">
          <div class="row mb-3">
            <div class="col">
              <label>Student No</label>
              <input type="text" name="student_no" id="edit_student_no" class="form-control" required>
            </div>
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
              <label>Gender</label>
              <select name="gender" id="edit_gender" class="form-select" required>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
            <div class="col">
              <label>Birthdate</label>
              <input type="date" name="birthdate" id="edit_birthdate" class="form-control">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col">
              <label>Year Level</label>
              <select name="year_level" id="edit_year_level" class="form-select" required>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
              </select>
            </div>
            <div class="col">
              <label>Program ID</label>
              <input type="number" name="program_id" id="edit_program_id" class="form-control" required>
            </div>
          </div>

          <button type="submit" class="btn btn-warning w-100">Update Student</button>
        </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/student.js"></script>
    <script>
    $(document).ready(function() {
        $('#studentAddForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'add_ajax.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#studentAddModal').modal('hide');
                        $('#studentAddForm')[0].reset();
                        alert('Student added successfully!');
                        location.reload();
                    } else {
                        alert(response.error || 'Failed to add student');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.log(xhr.responseText); // Add this for debugging
                    alert('Error adding student. Please try again.');
                }
            });
        });

        // Edit Student AJAX
        $('#studentEditForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'update_ajax.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#studentEditModal').modal('hide');
                        alert('Student updated successfully!');
                        location.reload();
                    } else {
                        alert(response.error || 'Failed to update student');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error updating student. Please try again.');
                }
            });
        });
    });

    // Edit Student function
    function editStudent(id) {
        $.ajax({
            url: 'get_student.php',
            type: 'GET',
            data: { student_id: id }, // Change 'id' to 'student_id'
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var student = response.data;
                    $('#edit_student_id').val(student.student_id);
                    $('#edit_student_no').val(student.student_no);
                    $('#edit_last_name').val(student.last_name);
                    $('#edit_first_name').val(student.first_name);
                    $('#edit_email').val(student.email);
                    $('#edit_gender').val(student.gender);
                    $('#edit_birthdate').val(student.birthdate);
                    $('#edit_year_level').val(student.year_level);
                    $('#edit_program_id').val(student.program_id);
                    $('#studentEditModal').modal('show');
                } else {
                    alert(response.error || 'Failed to load student data');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error loading student data');
            }
        });
    }

    // Delete Student function
    function deleteStudent(id) {
        if (confirm('Are you sure you want to delete this student?')) {
            $.ajax({
                url: 'delete_ajax.php', // <-- fix here
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