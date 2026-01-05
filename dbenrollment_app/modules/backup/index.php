<?php
/**
 * DATABASE BACKUP & RESTORE SYSTEM
 * DBA Enrollment System
 */
session_start();

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin'); // Only admins can access

// Set Philippines timezone
date_default_timezone_set('Asia/Manila');

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dbenrollment';

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Configuration
$backup_dir = '../../backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$message = "";
$error = "";

// ===== HANDLE BACKUP =====
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_backup'])) {
    $filename_option = $_POST['filename_option'];
    $custom_filename = trim($_POST['custom_filename'] ?? '');
    
    // Generate filename
    if ($filename_option === 'date') {
        $filename = 'dbenrollment_backup_' . date('Y-m-d_H-i-s') . '.sql';
    } else {
        if (empty($custom_filename)) {
            $error = "Please provide a custom filename.";
        } else {
            // Sanitize filename
            $custom_filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $custom_filename);
            $filename = $custom_filename . '_' . date('Y-m-d_H-i-s') . '.sql';
        }
    }
    
    if (empty($error)) {
        $backup_file = $backup_dir . $filename;
        
        // Execute mysqldump - Windows XAMPP
        $command = "\"C:\\xampp\\mysql\\bin\\mysqldump\" --user=root --host=localhost dbenrollment > \"{$backup_file}\"";
        
        exec($command, $output, $result);
        
        if ($result === 0 && file_exists($backup_file) && filesize($backup_file) > 0) {
            // Log backup activity
            $log_stmt = $conn->prepare("INSERT INTO backup_log (action, filename, file_size, created_at) VALUES ('backup', ?, ?, NOW())");
            $file_size = filesize($backup_file);
            $log_stmt->bind_param("si", $filename, $file_size);
            $log_stmt->execute();
            $log_stmt->close();
            
            $message = "Database backup created successfully: {$filename}";
        } else {
            $error = "Failed to create backup. Please check database credentials and permissions.";
        }
    }
}

// ===== HANDLE RESTORE =====
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['restore_backup'])) {
    $restore_file = $_POST['restore_file'];
    $backup_path = $backup_dir . $restore_file;
    
    if (!file_exists($backup_path)) {
        $error = "Backup file not found.";
    } else {
        // Execute mysql restore - Windows XAMPP
        $command = "\"C:\\xampp\\mysql\\bin\\mysql\" --user=root --host=localhost dbenrollment < \"{$backup_path}\"";
        
        exec($command, $output, $result);
        
        if ($result === 0) {
            // Log restore activity
            $log_stmt = $conn->prepare("INSERT INTO backup_log (action, filename, created_at) VALUES ('restore', ?, NOW())");
            $log_stmt->bind_param("s", $restore_file);
            $log_stmt->execute();
            $log_stmt->close();
            
            $message = "Database restored successfully from: {$restore_file}";
        } else {
            $error = "Failed to restore database. Please check the backup file integrity.";
        }
    }
}

// ===== HANDLE DELETE BACKUP =====
if (isset($_GET['delete'])) {
    $delete_file = $_GET['delete'];
    $delete_path = $backup_dir . $delete_file;
    
    if (file_exists($delete_path)) {
        if (unlink($delete_path)) {
            // Log deletion
            $log_stmt = $conn->prepare("INSERT INTO backup_log (action, filename, created_at) VALUES ('delete', ?, NOW())");
            $log_stmt->bind_param("s", $delete_file);
            $log_stmt->execute();
            $log_stmt->close();
            
            header("Location: index.php?status=deleted");
            exit();
        }
    }
}

// ===== HANDLE DOWNLOAD BACKUP =====
if (isset($_GET['download'])) {
    $download_file = $_GET['download'];
    $download_path = $backup_dir . $download_file;
    
    if (file_exists($download_path)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $download_file . '"');
        header('Content-Length: ' . filesize($download_path));
        readfile($download_path);
        exit();
    }
}

// Get list of existing backups
$backups = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backups[] = [
                'filename' => $file,
                'size' => filesize($backup_dir . $file),
                'date' => filemtime($backup_dir . $file)
            ];
        }
    }
    // Sort by date (newest first)
    usort($backups, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}

// Get recent backup logs
$logs_query = $conn->query("
    SELECT * FROM backup_log 
    ORDER BY created_at DESC 
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup & Restore - DBA Enrollment System</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
    <link rel="stylesheet" href="../../assets/css/backup.css">
</head>
    <style>
        .topbar {
            left: 249px;
        }
    </style>
<body>
    <?php include('../../templates/sidebar.php'); ?>
    <?php include('../../templates/header.php'); ?>

    <main class="main">
        <h1>Database Backup & Restore</h1>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                ✓ <?= htmlspecialchars($message) ?>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">×</span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ✗ <?= htmlspecialchars($error) ?>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">×</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
            <div class="alert alert-success">
                ✓ Backup file deleted successfully.
                <span class="close-btn" onclick="this.parentElement.style.display='none';">×</span>
            </div>
        <?php endif; ?>

        <!-- Backup & Restore Grid -->
        <div class="backup-grid">
            <!-- Create Backup -->
            <div class="backup-card">
                <h3>📦 Create Backup</h3>
                
                <form method="POST" id="backupForm">
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="filename_option" value="date" checked onchange="toggleCustomFilename()">
                            <span>Use current date & time</span>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="filename_option" value="custom" onchange="toggleCustomFilename()">
                            <span>Custom filename</span>
                        </label>
                    </div>

                    <div class="form-group" id="customFilenameGroup" style="display: none;">
                        <label for="custom_filename">Custom Filename</label>
                        <input
                            type="text"
                            id="custom_filename"
                            name="custom_filename"
                            placeholder="e.g., before_update, weekly_backup"
                            pattern="[a-zA-Z0-9_-]+"
                        >
                        <small>Only letters, numbers, hyphens, and underscores allowed</small>
                    </div>

                    <button type="submit" name="create_backup" class="btn btn-primary">
                        Create Backup
                    </button>
                </form>
            </div>

            <!-- Restore from Backup -->
            <div class="backup-card">
                <h3>⚠️ Restore Database</h3>

                <div class="warning-box">
                    <strong>Warning!</strong><br>
                    Restoring will replace all current data. Create a backup first!
                </div>

                <form method="POST" id="restoreForm" onsubmit="return confirmRestore()">
                    <div class="form-group">
                        <label for="restore_file">Select Backup File</label>
                        <select name="restore_file" id="restore_file" required>
                            <option value="">-- Choose a backup --</option>
                            <?php foreach ($backups as $backup): ?>
                                <option value="<?= htmlspecialchars($backup['filename']) ?>">
                                    <?= htmlspecialchars($backup['filename']) ?> 
                                    (<?= date('M j, Y g:i A', $backup['date']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="restore_backup" class="btn btn-warning">
                        Restore Database
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <h3>Total Backups</h3>
                <div class="stat-value"><?= count($backups) ?></div>
                <small>Backup files available</small>
            </div>

            <div class="stat-card">
                <h3>Total Size</h3>
                <div class="stat-value">
                    <?php 
                        $total_size = array_sum(array_column($backups, 'size'));
                        echo $total_size > 1048576 ? round($total_size / 1048576, 2) . ' MB' : round($total_size / 1024, 2) . ' KB';
                    ?>
                </div>
                <small>Disk space used</small>
            </div>

            <div class="stat-card">
                <h3>Latest Backup</h3>
                <div class="stat-value">
                    <?= !empty($backups) ? date('M j, Y', $backups[0]['date']) : 'None' ?>
                </div>
                <small><?= !empty($backups) ? date('g:i A', $backups[0]['date']) : 'No backups yet' ?></small>
            </div>
        </div>

        <!-- Existing Backups -->
        <div class="table-container">
            <h2>Available Backups (<?= count($backups) ?>)</h2>
            
            <?php if (!empty($backups)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Date Created</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td><?= htmlspecialchars($backup['filename']) ?></td>
                                <td><?= date('F j, Y - g:i A', $backup['date']) ?></td>
                                <td>
                                    <?= $backup['size'] > 1048576 
                                        ? round($backup['size'] / 1048576, 2) . ' MB' 
                                        : round($backup['size'] / 1024, 2) . ' KB' 
                                    ?>
                                </td>
                                <td>
                                    <a href="?download=<?= urlencode($backup['filename']) ?>" class="btn btn-download">
                                        Download
                                    </a>
                                    <a 
                                        href="?delete=<?= urlencode($backup['filename']) ?>" 
                                        class="btn btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this backup file?')"
                                    >
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>📂 No backups found. Create your first backup to get started.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activity Logs -->
        <?php if ($logs_query && $logs_query->num_rows > 0): ?>
            <div class="table-container">
                <h2>Recent Activity</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Filename</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = $logs_query->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-<?= $log['action'] ?>">
                                        <?= ucfirst($log['action']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($log['filename']) ?></td>
                                <td><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <script>
        function toggleCustomFilename() {
            const customOption = document.querySelector('input[name="filename_option"][value="custom"]');
            const customGroup = document.getElementById('customFilenameGroup');
            const customInput = document.getElementById('custom_filename');
            
            if (customOption.checked) {
                customGroup.style.display = 'block';
                customInput.required = true;
            } else {
                customGroup.style.display = 'none';
                customInput.required = false;
            }
        }

        function confirmRestore() {
            return confirm('⚠️ WARNING!\n\nThis will replace ALL current data with the backup.\n\nAre you absolutely sure you want to continue?');
        }

        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>