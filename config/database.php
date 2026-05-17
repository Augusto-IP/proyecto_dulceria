<?php
// Datos de la base de datos
$host     = 'localhost';
$db       = 'pasteleria_sistema_db';
$user     = 'root'; 
$password = '';    
$charset  = 'utf8mb4';
// Crear conexión PDO y exponerla como $pdo
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES => false,
];

try {
	$pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
	// Registrar el error y dejar $pdo en null para que el controlador lo detecte
	error_log('DB connection error: ' . $e->getMessage());
	$pdo = null;
}

?>