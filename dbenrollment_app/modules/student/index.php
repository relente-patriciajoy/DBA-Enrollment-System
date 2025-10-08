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

<div class="main">
    <h1>Student Management</h1>

    <div class="search-bar">
      <form method="GET">
        <input type="text" name="search" placeholder="Search student...">
        <button type="submit">Search</button>
      </form>
    </div>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
      + Add Student
    </button>

    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addModalLabel">Add New Student</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="addStudentForm">
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

    <table>
        <thead>
            <tr>
                <th>Student No</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Birthdate</th>
                <th>Year Level</th>
                <th>Program ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'tblstudent.php'; ?>
        </tbody>
    </table>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editStudentForm">
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
</body>
</html>