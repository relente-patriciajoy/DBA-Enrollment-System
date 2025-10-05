<?php
include_once '../../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Section Management</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
</head>
<body>
<?php include_once '../../templates/sidebar.php'; ?>

<div class="main">
    <h1>Sections</h1>

    <!-- Search -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search by section code...">
        <button type="submit">Search</button>
    </form>

    <!-- Buttons -->
    <button onclick="window.location.href='add.php'">Add Section</button>
    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Course ID</th>
                <th>Term ID</th>
                <th>Instructor ID</th>
                <th>Day Pattern</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Room ID</th>
                <th>Max Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'tblsection.php'; ?>
        </tbody>
    </table>
</div>
</body>
</html>