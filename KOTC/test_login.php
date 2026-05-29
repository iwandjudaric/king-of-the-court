<?php
require_once 'config/db_connection.php';

// Check if admins table exists and what's in it
try {
    $query = "SELECT * FROM admins";
    $stmt = $PDO->query($query);
    $admins = $stmt->fetchAll();
    
    echo "<h2>Admins in database:</h2>";
    if (empty($admins)) {
        echo "<p>❌ NO ADMIN ACCOUNTS FOUND!</p>";
    } else {
        echo "<pre>";
        print_r($admins);
        echo "</pre>";
        
        foreach ($admins as $admin) {
            echo "<h3>Testing admin: {$admin['username']}</h3>";
            echo "Stored hash: <code>{$admin['password']}</code><br>";
            
            // Test password verification
            echo "<br><strong>Testing passwords:</strong><br>";
            $test_passwords = ['admin123', 'admin', 'Admin123', '123456'];
            
            foreach ($test_passwords as $test_pass) {
                $result = password_verify($test_pass, $admin['password']);
                echo "password_verify('$test_pass', hash) = " . ($result ? '✅ TRUE' : '❌ FALSE') . "<br>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Also check if the password is plain text (not a hash)
echo "<h2>Generate new password hashes:</h2>";
echo "Hash for 'admin123': <code>" . password_hash('admin123', PASSWORD_BCRYPT) . "</code><br>";
?>
