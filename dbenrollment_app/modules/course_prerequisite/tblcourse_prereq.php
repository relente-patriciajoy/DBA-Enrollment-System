<?php
// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$new_prereq_id = isset($_GET['new_prereq']) ? intval($_GET['new_prereq']) : 0;

// Build query with JOINs to get course names
$sql = "SELECT 
            cp.prereq_id,
            cp.course_id,
            cp.prerequisite_course_id,
            c1.course_code as course_code,
            c1.course_title as course_title,
            c2.course_code as prereq_course_code,
            c2.course_title as prereq_course_title
        FROM tblcourse_prerequisite cp
        INNER JOIN tblcourse c1 ON cp.course_id = c1.course_id
        INNER JOIN tblcourse c2 ON cp.prerequisite_course_id = c2.course_id
        WHERE cp.is_deleted = 0 
        AND c1.is_deleted = 0 
        AND c2.is_deleted = 0";

if(!empty($search)) {
    $sql .= " AND (c1.course_code LIKE ? OR c1.course_title LIKE ? OR c2.course_code LIKE ? OR c2.course_title LIKE ?)";
}

// If there's a new prereq, show it first, then sort the rest alphabetically by course code
if($new_prereq_id > 0) {
    $sql .= " ORDER BY (cp.prereq_id = ?) DESC, c1.course_code ASC";
} else {
    $sql .= " ORDER BY c1.course_code ASC";
}

// Prepare statement
$stmt = $conn->prepare($sql);

if(!empty($search) && $new_prereq_id > 0) {
    $searchParam = "%{$search}%";
    $stmt->bind_param("ssssi", $searchParam, $searchParam, $searchParam, $searchParam, $new_prereq_id);
} elseif(!empty($search)) {
    $searchParam = "%{$search}%";
    $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
} elseif($new_prereq_id > 0) {
    $stmt->bind_param("i", $new_prereq_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Display results
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Add highlight class if this is the newly added prerequisite
        $highlight_class = ($new_prereq_id > 0 && $row['prereq_id'] == $new_prereq_id) ? 'new-prereq-highlight' : '';
        
        echo "<tr class='{$highlight_class}'>";
        echo "<td>{$row['prereq_id']}</td>";
        echo "<td>{$row['course_code']}</td>";
        echo "<td>{$row['course_title']}</td>";
        echo "<td>{$row['prereq_course_code']}</td>";
        echo "<td>{$row['prereq_course_title']}</td>";
        echo "<td>
                <button class='btn btn-warning btn-sm' onclick='editPrereq({$row['prereq_id']})'>Edit</button>
                <button class='btn btn-danger btn-sm' onclick='deletePrereq({$row['prereq_id']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No prerequisites found</td></tr>";
}

$stmt->close();
?>