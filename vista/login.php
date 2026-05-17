<?php
session_start();
require_once __DIR__ . '/../controlador/authController.php';

$mensaje = '';
$usuario = '';

if (isset($_SESSION['id_usuario'])) {
    header('Location: home.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === '' || $password === '') {
        $mensaje = 'Complete todos los campos';
    } else {
        $auth = new authController();
        if ($auth->login($usuario, $password)) {
            header('Location: home.php');
            exit();
        }
        $mensaje = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pastelería</title>
    <link rel="stylesheet" href="../activos/css/login.css">
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <?php if ($mensaje): ?>
            <div class="error" style="color: red; margin-bottom: 15px;"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required value="<?php echo htmlspecialchars($usuario); ?>">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>