<div class="topbar">
  <div class="topbar-left">
    <img src="../assets/images/pup-logo.png" alt="PUP Logo" class="system-logo">
    <h2>Enrollment Management System</h2>
  </div>
  <div class="topbar-center">
    <span>
      <?php
        // Dynamically show the current page name
        $pageTitle = basename($_SERVER['PHP_SELF'], ".php");
        echo ucfirst($pageTitle); // e.g., "Student", "Program", etc.
      ?>
    </span>
  </div>
  <div class="topbar-right">
    <span class="user-role">
      <?php echo $_SESSION['role'] ?? 'Admin'; ?>
    </span>
    <a href="../logout.php" class="logout-btn">Logout</a>
  </div>
</div>
