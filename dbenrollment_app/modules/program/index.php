<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Program Management</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
</head>
<body>

<?php include_once '../../templates/sidebar.php'; ?>

<div class="main">
    <h1>Program Management</h1>

    <!-- Search -->
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search program...">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Actions -->
    <button onclick="window.location.href='add.php'">Add Program</button>
    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>Program Code</th>
                <th>Program Name</th>
                <th>Department ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'tblprogram.php'; ?>
        </tbody>
    </table>
</div>

</body>
</html>