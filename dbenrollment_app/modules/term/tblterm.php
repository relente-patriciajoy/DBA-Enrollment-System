<?php
// Prevent "Notice: session_start(): Ignoring session_start()"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use include_once to prevent "Fatal error: Cannot redeclare requireRole()"
include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');
requireRole('admin');

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$new_term_id = isset($_GET['new_term']) ? intval($_GET['new_term']) : 0;

// Build query
$sql = "SELECT * FROM tblterm WHERE is_deleted = 0";

if(!empty($search)) {
    $sql .= " AND term_code LIKE ?";
}

// If there's a new term, show it first, then sort the rest by date (oldest first - chronological order)
if($new_term_id > 0) {
    $sql .= " ORDER BY (term_id = ?) DESC, start_date ASC";
} else {
    $sql .= " ORDER BY start_date ASC";
}

// Prepare statement
$stmt = $conn->prepare($sql);

if(!empty($search) && $new_term_id > 0) {
    $searchParam = "%{$search}%";
    $stmt->bind_param("si", $searchParam, $new_term_id);
} elseif(!empty($search)) {
    $searchParam = "%{$search}%";
    $stmt->bind_param("s", $searchParam);
} elseif($new_term_id > 0) {
    $stmt->bind_param("i", $new_term_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Display results
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Add highlight class if this is the newly added term
        $highlight_class = ($new_term_id > 0 && $row['term_id'] == $new_term_id) ? 'new-term-highlight' : '';

        echo "<tr class='{$highlight_class}'>";
        echo "<td>{$row['term_id']}</td>";
        echo "<td>{$row['term_code']}</td>";
        echo "<td>" . date('F d, Y', strtotime($row['start_date'])) . "</td>";
        echo "<td>" . date('F d, Y', strtotime($row['end_date'])) . "</td>";
        echo "<td>
                <button class='btn btn-warning btn-sm' onclick='editTerm({$row['term_id']})'>Edit</button>
                <button class='btn btn-danger btn-sm' onclick='deleteTerm({$row['term_id']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No terms found</td></tr>";
}

$stmt->close();
?>