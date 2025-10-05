<?php
include_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM tblterm WHERE term_id=$id");
$row = $res->fetch_assoc();
if (!$row) { echo "Record not found."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $term_code = $_POST['term_code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("UPDATE tblterm SET term_code=?, start_date=?, end_date=? WHERE term_id=?");
    $stmt->bind_param("sssi", $term_code, $start_date, $end_date, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Term</title></head>
<body>
<h2>Edit Term</h2>
<form method="POST">
    <label>Term Code</label><br>
    <input type="text" name="term_code" value="<?php echo $row['term_code']; ?>" required><br><br>

    <label>Start Date</label><br>
    <input type="date" name="start_date" value="<?php echo $row['start_date']; ?>" required><br><br>

    <label>End Date</label><br>
    <input type="date" name="end_date" value="<?php echo $row['end_date']; ?>" required><br><br>

    <button type="submit">Update</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>