<?php
/**
 * Verify Auth Headers
 * Checks if all files have proper authentication
 */

$modules = [
    'backup', 'course', 'course_prerequisite', 'department',
    'enrollment', 'instructor', 'program', 'room',
    'section', 'student', 'term'
];

$results = [
    'protected' => [],
    'missing_auth' => [],
    'has_session' => [],
    'ajax_files' => [],
    'regular_files' => []
];

echo "🔍 Verifying authentication headers...\n\n";

foreach ($modules as $module) {
    $dir = "modules/$module/";
    
    if (!is_dir($dir)) continue;
    
    echo "📁 Checking module: $module\n";
    
    $files = glob($dir . "*.php");
    
    foreach ($files as $file) {
        $basename = basename($file);
        $content = file_get_contents($file);

        // Check if it has auth_check
        $hasAuthCheck = strpos($content, 'auth_check') !== false;
        $hasSessionStart = strpos($content, 'session_start()') !== false;
        $hasRoleCheck = strpos($content, 'role_check') !== false;
        $hasRequireRole = strpos($content, 'requireRole') !== false;

        // Determine file type
        $isAjax = (
            strpos($basename, '.php') !== false ||
            strpos($basename, 'add_') === 0 ||
            strpos($basename, 'delete_') === 0 ||
            strpos($basename, 'update_') === 0 ||
            strpos($basename, 'get_') === 0
        ) && $basename !== 'index.php';

        if ($hasAuthCheck && $hasSessionStart && $hasRoleCheck && $hasRequireRole) {
            echo "   ✅ PROTECTED: $basename\n";
            $results['protected'][] = "$module/$basename";

            if ($isAjax) {
                $results['ajax_files'][] = "$module/$basename";
            } else {
                $results['regular_files'][] = "$module/$basename";
            }
        } elseif ($hasSessionStart && !$hasAuthCheck) {
            echo "   ⚠️  HAS SESSION BUT NO AUTH: $basename\n";
            $results['has_session'][] = "$module/$basename";
        } else {
            echo "   ❌ MISSING AUTH: $basename\n";
            $results['missing_auth'][] = "$module/$basename";
        }
    }
    
    echo "\n";
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 VERIFICATION SUMMARY\n";
echo str_repeat("=", 60) . "\n\n";

echo "✅ Protected Files: " . count($results['protected']) . "\n";
echo "   - Regular Pages: " . count($results['regular_files']) . "\n";
echo "   - AJAX Endpoints: " . count($results['ajax_files']) . "\n\n";

if (!empty($results['missing_auth'])) {
    echo "❌ Missing Authentication (" . count($results['missing_auth']) . " files):\n";
    foreach ($results['missing_auth'] as $file) {
        echo "   - $file\n";
    }
    echo "\n";
}

if (!empty($results['has_session'])) {
    echo "⚠️  Has Session but No Auth (" . count($results['has_session']) . " files):\n";
    foreach ($results['has_session'] as $file) {
        echo "   - $file\n";
    }
    echo "\n";
}

echo str_repeat("=", 60) . "\n";

if (empty($results['missing_auth']) && empty($results['has_session'])) {
    echo "🎉 SUCCESS! All files are properly protected!\n";
} else {
    echo "⚠️  Some files need attention. Run update script again.\n";
}

echo str_repeat("=", 60) . "\n";
?>