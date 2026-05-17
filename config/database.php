<?php
// ============================================================
//  config/database.php — Conexión PDO
// ============================================================
 
$host    = 'localhost';
$db      = 'pasteleria_sistema_';
$user    = 'root';
$password = '';
$charset = 'utf8mb4';
 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
 
try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    error_log('DB connection error: ' . $e->getMessage());
    $pdo = null;
}