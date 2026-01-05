<?php
// Always start PHP session first, before any HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="topbar">
  <div class="topbar-left">
    <img src="../../../dbenrollment_app/assets/images/plogo.png" alt="PUP Logo" class="system-logo">
    <h2>Enrollment Management System</h2>
  </div>

  <div class="topbar-center">
    <span>
      <?php
        // 1. Get the current directory path
        $currentDir = basename(dirname($_SERVER['PHP_SELF']));

        // 2. Format the folder name (e.g., "department" becomes "Department Management")
        // You can add a mapping for specific clean names
        $pageNames = [
            'enrollment' => 'Enrollment Management',
            'department' => 'Department Management',
            'room'       => 'Room Management',
            'student'    => 'Student Management',
            'course'     => 'Course Management',
            'backup'     => 'Database Backup & Restore'
        ];

        // 3. Display the mapped name, or a formatted version of the folder name as a fallback
        if (isset($pageNames[$currentDir])) {
            echo $pageNames[$currentDir];
        } else {
            echo ucfirst($currentDir) . " Management";
        }
      ?>
    </span>
  </div>

  <div class="topbar-right">
    <span class="user-role">
      <?php
        // Get the role from session, default to 'Admin' if not set
        $role = $_SESSION['role'] ?? 'Admin';

        // If the role is 'admin', display 'Administrator', otherwise show the role as is
        echo (strtolower($role) === 'admin') ? 'Administrator' : ucfirst($role);
      ?>
    </span>
    <a href="../../logout.php" class="logout-btn">Logout</a>
  </div>
</div>