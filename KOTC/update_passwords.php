<?php
require_once 'config/db_connection.php';

// Hash the passwords properly
$admins_to_update = [
    ['id' => 4, 'username' => 'admin', 'plain_password' => 'admin123'],
    ['id' => 5, 'username' => 'admin1', 'plain_password' => 'admin1234']
];

echo "<h2>Updating admin passwords to bcrypt hashes:</h2>";

try {
    foreach ($admins_to_update as $admin) {
        $hashed = password_hash($admin['plain_password'], PASSWORD_BCRYPT);
        
        $query = "UPDATE admins SET password = :password WHERE id = :id";
        $stmt = $PDO->prepare($query);
        $stmt->execute([
            ':password' => $hashed,
            ':id' => $admin['id']
        ]);
        
        echo "<p>✅ Updated {$admin['username']} with hash:</p>";
        echo "<code style='word-break: break-all;'>{$hashed}</code><br>";
    }
    
    // Verify the update worked
    echo "<h2>Verification - Testing passwords again:</h2>";
    $query = "SELECT * FROM admins WHERE id IN (4, 5)";
    $stmt = $PDO->query($query);
    $admins = $stmt->fetchAll();
    
    foreach ($admins as $admin) {
        echo "<h3>Testing {$admin['username']}:</h3>";
        $verify_admin123 = password_verify('admin123', $admin['password']);
        $verify_admin1234 = password_verify('admin1234', $admin['password']);
        
        echo "password_verify('admin123') = " . ($verify_admin123 ? '✅ TRUE' : '❌ FALSE') . "<br>";
        echo "password_verify('admin1234') = " . ($verify_admin1234 ? '✅ TRUE' : '❌ FALSE') . "<br>";
    }
    
    echo "<h2>✅ Done! Try logging in now.</h2>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
