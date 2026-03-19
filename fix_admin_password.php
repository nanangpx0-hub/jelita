<?php
/**
 * Fix Script: Reset Admin Password to 'password'
 * SISE2026 BPS Kabupaten Jember
 * 
 * Run this script ONCE to fix the admin password issue.
 * Access via: http://localhost/se2026-jember/fix_admin_password.php
 */

require_once __DIR__ . '/config/config.php';

echo "<h2>SISE2026 - Reset Admin Password</h2>";
echo "<hr>";

// Check database connection
if (!$pdo) {
    echo "<p style='color:red;'><strong>❌ ERROR:</strong> Database connection failed!</p>";
    echo "<p>Please check your database credentials.</p>";
    exit;
}

echo "<p style='color:green;'><strong>✓ Database Connected</strong></p>";

// Check if admin exists
try {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo "<p style='color:red;'><strong>❌ Admin user not found!</strong></p>";
        echo "<p>Please import the seed data first: sql/seed_dummy_data.sql</p>";
        exit;
    }
    
    echo "<p><strong>Found admin user:</strong> {$admin['username']} (ID: {$admin['id']})</p>";
    
    // Generate new password hash for 'password'
    $newPassword = 'password';
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    echo "<p><strong>New Password:</strong> {$newPassword}</p>";
    echo "<p><strong>New Hash:</strong> <code>" . htmlspecialchars($newHash) . "</code></p>";
    
    // Update the password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE username = 'admin'");
    $result = $stmt->execute([':hash' => $newHash]);
    
    if ($result) {
        echo "<p style='color:green; font-size:18px;'><strong>✓ SUCCESS!</strong></p>";
        echo "<p>The admin password has been reset to: <strong style='background:yellow; padding:5px 10px;'>password</strong></p>";
        
        echo "<h3>You can now login with:</h3>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> admin</li>";
        echo "<li><strong>Password:</strong> password</li>";
        echo "</ul>";
        
        echo "<p style='color:blue;'><em>Note: Other demo accounts (operator.jember, pml.kaliwates, pcl.sumbersari) still use password: DemoSE2026!</em></p>";
        
    } else {
        echo "<p style='color:red;'><strong>❌ FAILED to update password!</strong></p>";
        echo "<p>Please check database permissions.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php?page=login' style='padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Go to Login Page</a></p>";
?>
