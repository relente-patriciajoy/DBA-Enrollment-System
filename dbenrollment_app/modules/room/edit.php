<?php
include_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM tblroom WHERE room_id=$id");
$row = $res->fetch_assoc();
if (!$row) { echo "Record not found."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_code = $_POST['room_code'];
    $building = $_POST['building'];
    $capacity = (int)$_POST['capacity'];

    $stmt = $conn->prepare("UPDATE tblroom SET room_code=?, building=?, capacity=? WHERE room_id=?");
    $stmt->bind_param("ssii", $room_code, $building, $capacity, $id);

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
<head><title>Edit Room</title></head>
<body>
<h2>Edit Room</h2>
<form method="POST">
    <label>Room Code</label><br>
    <input type="text" name="room_code" value="<?php echo $row['room_code']; ?>" required><br><br>

    <label>Building</label><br>
    <input type="text" name="building" value="<?php echo $row['building']; ?>" required><br><br>

    <label>Capacity</label><br>
    <input type="number" name="capacity" value="<?php echo $row['capacity']; ?>" required><br><br>

    <button type="submit">Update</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>