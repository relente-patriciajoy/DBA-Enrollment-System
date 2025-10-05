<?php
include_once '../../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Prerequisites</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
</head>
<body>
<?php include_once '../../templates/sidebar.php'; ?>

<div class="main">
    <h1>Course Prerequisites</h1>

    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by Course ID...">
            <button type="submit">Search</button>
        </form>
    </div>

    <button onclick="window.location.href='add.php'">Add Prerequisite</button>
    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>

    <table>
        <thead>
            <tr>
                <th>Prereq ID</th>
                <th>Course ID</th>
                <th>Prerequisite Course ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'tblcourse_prereq.php'; ?>
        </tbody>
    </table>
</div>
</body>
</html>