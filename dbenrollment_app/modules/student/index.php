<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
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

    <button onclick="window.location.href='add.php'">Add Student</button>
    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>

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

</body>
</html>