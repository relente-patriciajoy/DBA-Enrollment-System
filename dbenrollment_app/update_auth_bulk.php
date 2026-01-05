<?php
/**
 * Bulk Auth Header Updater & Path Fixer
 * Run once: php update_auth_bulk.php
 */

// Define patterns for different file types
$patterns = [
    // Regular PHP pages
    'regular' => [
        'search' => '/^<\?php\s*\n/',
        'replace' => "<?php\nsession_start();\ninclude('../includes/auth_check.php');\ninclude('../includes/role_check.php');\nrequireRole('admin');\n\n",
        'files' => ['index.php', 'add.php', 'edit.php', 'delete.php']
    ],
    
    // AJAX files
    'ajax' => [
        'search' => '/^<\?php\s*\n(session_start\(\);\s*\n)?/',
        'replace' => "<?php\nsession_start();\nheader('Content-Type: application/json');\n\ninclude('../includes/auth_check_ajax.php');\ninclude('../includes/role_check_ajax.php');\nrequireRoleAjax('admin');\n\n",
        'files' => ['*_ajax.php', 'add_*.php', 'delete_*.php', 'update_*.php', 'get_*.php']
    ],
    
    // Export files
    'export' => [
        'search' => '/^<\?php\s*\n/',
        'replace' => "<?php\nsession_start();\ninclude('../includes/auth_check.php');\ninclude('../includes/role_check.php');\nrequireRole('admin');\n\n",
        'files' => ['export_*.php']
    ]
];

$modules = [
    'backup', 'course', 'course_prerequisite', 'department', 
    'enrollment', 'instructor', 'program', 'room', 
    'section', 'student', 'term'
];

$updated = 0;
$skipped = 0;
$errors = 0;
$fixedPaths = 0;

echo "🚀 Starting bulk auth header update and path correction...\n\n";

foreach ($modules as $module) {
    $dir = "modules/$module/";
    
    if (!is_dir($dir)) {
        echo "⚠️  Directory not found: $dir\n";
        continue;
    }
    
    echo "📁 Processing module: $module\n";
    
    $files = glob($dir . "*.php");
    
    foreach ($files as $file) {
        $basename = basename($file);
        $content = file_get_contents($file);
        $originalContent = $content;

        // --- STEP 1: FIX INCORRECT PATHS IF THEY EXIST ---
        if (strpos($content, '../../includes/') !== false) {
            $content = str_replace('../../includes/', '../includes/', $content);
            $fixedPaths++;
        }

        // --- STEP 2: APPLY PROTECTION IF MISSING ---
        if (strpos($content, 'auth_check') === false) {
            // Determine file type
            $isAjax = (
                strpos($basename, '_ajax.php') !== false ||
                strpos($basename, 'add_') === 0 ||
                strpos($basename, 'delete_') === 0 ||
                strpos($basename, 'update_') === 0 ||
                strpos($basename, 'get_') === 0
            ) && $basename !== 'index.php';

            $isExport = strpos($basename, 'export_') === 0;

            // Choose pattern
            if ($isAjax) {
                $pattern = $patterns['ajax'];
                $type = 'AJAX';
            } elseif ($isExport) {
                $pattern = $patterns['export'];
                $type = 'EXPORT';
            } else {
                $pattern = $patterns['regular'];
                $type = 'PAGE';
            }

            // Remove existing session_start if present to avoid duplicates
            $content = preg_replace('/^<\?php\s*\nsession_start\(\);\s*\n/', "<?php\n", $content);

            // Apply pattern
            $content = preg_replace($pattern['search'], $pattern['replace'], $content, 1);
        }

        // --- STEP 3: SAVE CHANGES ---
        if ($content !== $originalContent) {
            if (file_put_contents($file, $content)) {
                echo "   ✅ Updated/Fixed: $basename\n";
                $updated++;
            } else {
                echo "   ❌ Failed to write: $basename\n";
                $errors++;
            }
        } else {
            echo "   ⏭️  Skipped (Already correct): $basename\n";
            $skipped++;
        }
    }
    echo "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 Summary:\n";
echo "   ✅ Total Files Modified: $updated\n";
echo "   🔗 Paths Corrected: $fixedPaths\n";
echo "   ⏭️  Files Skipped: $skipped\n";
echo "   ❌ Errors: $errors\n";
echo str_repeat("=", 50) . "\n";
echo "\n✨ Done! Your file paths should now be correct.\n";
?>