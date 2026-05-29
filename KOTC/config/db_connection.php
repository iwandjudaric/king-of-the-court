<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = 'localhost';
$port = '3306';
$username = '102381_kotc';
$password = '102381!@#_kotc';
$dbname = 'King-of-the-courtz';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

$PDO = null;

try {
    $PDO = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password, $options);
} catch (PDOException $e) {
    die("Database-fout: kan niet verbinden");
}
?>
