<!-- http://localhost/DBA-Enrollment-System/dbenrollment_app/modules/students/index.php -->
<!-- Dashboard -->
 <?php
include_once '../../config/database.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Maintenance</title>
    <style>
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
        }
    </style>
</head>
<body>

<?php include_once '../../templates/sidebar.php'; ?>

<div class="main-content">
    <h2>Student Management</h2>

    <!-- Search Form -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search student..." />
        <button type="submit">Search</button>
    </form>
    <br>

    <!-- Action Buttons -->
    <button onclick="window.location.href='add.php'">Add Student</button>
    <button onclick="window.print()">Print</button>
    <br><br>

    <!-- Student Table -->
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
            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            $sql = "SELECT * FROM tblstudent
                    WHERE student_no LIKE '%$search%'
                    OR last_name LIKE '%$search%'
                    OR first_name LIKE '%$search%'
                    ORDER BY student_id ASC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['student_no']}</td>
                        <td>{$row['last_name']}</td>
                        <td>{$row['first_name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['birthdate']}</td>
                        <td>{$row['year_level']}</td>
                        <td>{$row['program_id']}</td>
                        <td>
                            <a href='edit.php?id={$row['student_id']}'>Edit</a> |
                            <a href='delete.php?id={$row['student_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
