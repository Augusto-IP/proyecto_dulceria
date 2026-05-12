<?php
// Configuración de la base de datos
$host = 'localhost';
$db   = 'dona_solina'; // Nombre de la base de datos que creamos
$user = 'root';        // Usuario por defecto de XAMPP
$pass = '';            // Por defecto XAMPP no tiene contraseña
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=dona_solina", "root", "");
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>