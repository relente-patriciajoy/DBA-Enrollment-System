<?php
include_once '../../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <title>Department Maintenance</title>
</head>
<body>
<?php include_once '../../templates/sidebar.php'; ?>

<div class="main-content">
    <h2>Department Management</h2>

    <!-- Search -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search department..." />
        <button type="submit">Search</button>
    </form>
    <br>

    <!-- Action Buttons -->
    <button onclick="window.location.href='add.php'">Add Department</button>
    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>
    <br><br>

    <!-- Table -->
    <?php include 'tbldepartment.php'; ?>
</div>
</body>
</html>