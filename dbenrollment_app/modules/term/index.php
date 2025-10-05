<?php
include_once '../../config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Term Management</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
</head>
<body>
<?php include_once '../../templates/sidebar.php'; ?>

<div class="main">
    <h1>Terms</h1>

    <!-- Search -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search term code..." />
        <button type="submit">Search</button>
    </form>

    <!-- Buttons -->
    <button onclick="window.location.href='add.php'">Add Term</button>
    <button onclick="window.location.href='export_excel.php'">Export to Excel</button>
    <button onclick="window.location.href='export_pdf.php'">Export to PDF</button>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Term Code</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php include 'tblterm.php'; ?>
        </tbody>
    </table>
</div>
</body>
</html>