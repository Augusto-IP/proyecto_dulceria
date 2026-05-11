<?php
// Configuración de la base de datos
$host = 'localhost';
$db   = 'dona_solina'; // Nombre de la base de datos que creamos
$user = 'root';        // Usuario por defecto de XAMPP
$pass = '';            // Por defecto XAMPP no tiene contraseña
$charset = 'utf8mb4';

// Configuración de opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Intentar realizar la conexión
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Descomenta la línea de abajo solo para probar que funciona
    // echo "Conexión exitosa a la base de datos de Doña Solina"; 
} catch (\PDOException $e) {
    // Si hay un error, lo muestra
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>