<?php
/**
 * Diagnostic Script: Check Admin User in Database
 * SISE2026 BPS Kabupaten Jember
 */

require_once __DIR__ . '/config/config.php';

echo "<h2>SISE2026 - Admin User Diagnostic Check</h2>";
echo "<hr>";

// Check database connection
if (!$pdo) {
    echo "<p style='color:red;'><strong>❌ ERROR:</strong> Database connection failed!</p>";
    echo "<p>Please check your database credentials in config.php</p>";
    exit;
}

echo "<p style='color:green;'><strong>✓ Database Connection:</strong> Successful</p>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Host:</strong> " . DB_HOST . ":" . DB_PORT . "</p>";
echo "<hr>";

// Check if users table exists
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "<p style='color:green;'><strong>✓ Users Table:</strong> Exists ($count users)</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>❌ Users Table:</strong> Not found or error accessing</p>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

echo "<hr>";

// Get all users
try {
    $stmt = $pdo->query("SELECT id, username, nama_lengkap, email, role, is_active, last_login, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    echo "<h3>All Users in Database:</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse; width:100%;'>";
    echo "<tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Active</th>
            <th>Last Login</th>
          </tr>";
    
    foreach ($users as $user) {
        $active = $user['is_active'] ? 'Yes' : 'No';
        $lastLogin = $user['last_login'] ?? 'Never';
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td><strong>{$user['username']}</strong></td>";
        echo "<td>{$user['nama_lengkap']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$active}</td>";
        echo "<td>{$lastLogin}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>ERROR:</strong> Could not retrieve users</p>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";

// Specifically check admin user
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    echo "<h3>Admin User Details:</h3>";
    
    if ($admin) {
        echo "<p style='color:green;'><strong>✓ Admin user EXISTS</strong></p>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> {$admin['id']}</li>";
        echo "<li><strong>Username:</strong> {$admin['username']}</li>";
        echo "<li><strong>Full Name:</strong> {$admin['nama_lengkap']}</li>";
        echo "<li><strong>Email:</strong> {$admin['email']}</li>";
        echo "<li><strong>Role:</strong> {$admin['role']}</li>";
        echo "<li><strong>Is Active:</strong> " . ($admin['is_active'] ? 'Yes' : 'No') . "</li>";
        echo "<li><strong>Password Hash:</strong> <code>" . htmlspecialchars($admin['password_hash']) . "</code></li>";
        echo "<li><strong>Last Login:</strong> " . ($admin['last_login'] ?? 'Never') . "</li>";
        echo "</ul>";
        
        // Test password verification
        echo "<h3>Password Test:</h3>";
        
        $passwords_to_test = ['password', 'DemoSE2026!', 'admin123'];
        
        foreach ($passwords_to_test as $test_pass) {
            $matches = password_verify($test_pass, $admin['password_hash']);
            $status = $matches ? '✓ MATCHES' : '❌ does not match';
            $color = $matches ? 'green' : 'red';
            echo "<p style='color:{$color};'><strong>Password '{$test_pass}':</strong> {$status}</p>";
        }
        
        echo "<p><em>Note: If none of these passwords match, the hash may have been generated differently.</em></p>";
        
    } else {
        echo "<p style='color:red;'><strong>❌ Admin user DOES NOT EXIST!</strong></p>";
        echo "<p>You need to create the admin user or import the seed data.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";

// Show expected password hashes
echo "<h3>Reference: Expected Password Hashes</h3>";
echo "<ul>";
echo "<li><strong>'password'</strong> hash should start with: <code>\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi</code></li>";
echo "<li><strong>'DemoSE2026!'</strong> hash should start with: <code>\$2y\$10\$s7IZyQCj25/V0rw8MM.6uOngY4eISUL2JqLjsbq5C19O3JoxN5.Mi</code></li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Generated: " . date('Y-m-d H:i:s') . "</em></p>";
?>
