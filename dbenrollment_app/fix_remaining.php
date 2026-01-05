<?php
/**
 * Fix the 10 remaining files that need auth headers
 */

$files_to_fix = [
    'modules/course/index.php',
    'modules/course_prerequisite/index.php',
    'modules/department/index.php',
    'modules/enrollment/index.php',
    'modules/enrollment/irregular_students.php',
    'modules/instructor/index.php',
    'modules/program/index.php',
    'modules/room/index.php',
    'modules/section/index.php',
    'modules/student/index.php'
];

$auth_header = "<?php
session_start();
include('../../includes/auth_check.php');
include('../../includes/role_check.php');
requireRole('admin');

";

echo "🔧 Fixing remaining 10 files...\n\n";

$fixed = 0;
$errors = 0;

foreach ($files_to_fix as $file) {
    if (!file_exists($file)) {
        echo "❌ File not found: $file\n";
        $errors++;
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Check if already has auth
    if (strpos($content, 'auth_check.php') !== false) {
        echo "⏭️  Already protected: $file\n";
        continue;
    }
    
    // Remove existing session_start if present
    $content = preg_replace('/^<\?php\s*\n(session_start\(\);\s*\n)?/', '', $content);
    
    // Add auth header
    $newContent = $auth_header . ltrim($content);
    
    if (file_put_contents($file, $newContent)) {
        echo "✅ Fixed: $file\n";
        $fixed++;
    } else {
        echo "❌ Failed to write: $file\n";
        $errors++;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 Results:\n";
echo "   ✅ Fixed: $fixed files\n";
echo "   ❌ Errors: $errors files\n";
echo str_repeat("=", 50) . "\n";

if ($fixed > 0) {
    echo "\n✨ Success! Run verify_auth.php again to confirm.\n";
}
?>