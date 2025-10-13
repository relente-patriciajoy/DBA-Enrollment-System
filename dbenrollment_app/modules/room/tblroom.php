<?php
// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$new_room_id = isset($_GET['new_room']) ? intval($_GET['new_room']) : 0;

// Build query
$sql = "SELECT * FROM tblroom WHERE is_deleted = 0";

if(!empty($search)) {
    $sql .= " AND (room_code LIKE ? OR building LIKE ?)";
}

// If there's a new room, show it first, then sort the rest alphabetically by room code
if($new_room_id > 0) {
    $sql .= " ORDER BY (room_id = ?) DESC, room_code ASC";
} else {
    $sql .= " ORDER BY room_code ASC";
}

// Prepare statement
$stmt = $conn->prepare($sql);

if(!empty($search) && $new_room_id > 0) {
    $searchParam = "%{$search}%";
    $stmt->bind_param("ssi", $searchParam, $searchParam, $new_room_id);
} elseif(!empty($search)) {
    $searchParam = "%{$search}%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
} elseif($new_room_id > 0) {
    $stmt->bind_param("i", $new_room_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Display results
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Add highlight class if this is the newly added room
        $highlight_class = ($new_room_id > 0 && $row['room_id'] == $new_room_id) ? 'new-room-highlight' : '';

        echo "<tr class='{$highlight_class}'>";
        echo "<td>{$row['room_id']}</td>";
        echo "<td>{$row['room_code']}</td>";
        echo "<td>{$row['building']}</td>";
        echo "<td>{$row['capacity']}</td>";
        echo "<td>
                <button class='btn btn-warning btn-sm' onclick='editRoom({$row['room_id']})'>Edit</button>
                <button class='btn btn-danger btn-sm' onclick='deleteRoom({$row['room_id']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No rooms found</td></tr>";
}

$stmt->close();
?>