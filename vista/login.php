<?php
session_start();
require_once __DIR__ . '/../controlador/authController.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? ''); 
    $pass = trim($_POST['password'] ?? '');

    if ($user === '' || $pass === '') {
        $mensaje = 'Complete todos los campos';
    } else {
        $auth = new authController(); 
        if ($auth->login($user, $pass)) {
            header('Location: home.php');
            exit;
        } else {
            $mensaje = 'Usuario o clave incorrectos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../activos/css/login.css">
    <title>Dulceria</title>
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <form action="login.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>