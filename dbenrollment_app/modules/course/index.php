<?php
include_once '../../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Management</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
</head>
<body>
<?php include_once '../../templates/sidebar.php'; ?>

<div class="main">
    <h1>Course Management</h1>

    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search course...">
            <button type="submit">Search</button>
        </form>
    </div>

    <button onclick="window.location.href='add.php'">Add Course</button>
    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Lecture Hrs</th>
                <th>Lab Hrs</th>
                <th>Units</th>
                <th>Dept ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'tblcourse.php'; ?>
        </tbody>
    </table>
</div>
</body>
</html>