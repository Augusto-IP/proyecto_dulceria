<?php
// ============================================================
//  vista/login.php
// ============================================================
session_start();
 
if (isset($_SESSION['id_usuario'])) {
    header('Location: home.php');
    exit();
}
 
require_once __DIR__ . '/../controlador/authController.php';
 
$mensaje = '';
$usuario = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario']  ?? '');
    $password = trim($_POST['password'] ?? '');
 
    if ($usuario === '' || $password === '') {
        $mensaje = 'Completa todos los campos.';
    } else {
        $auth = new authController();
        if ($auth->login($usuario, $password)) {
            // Todos los usuarios van al home
            header('Location: home.php');
            exit();
        }
        $mensaje = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Pastelería IP</title>
    <link rel="stylesheet" href="../activos/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-logo">
                <div class="login-logo-icon"><i class="fas fa-birthday-cake"></i></div>
                <h1>Pastelería IP</h1>
                <p>Inicia sesión para continuar</p>
            </div>
 
            <?php if ($mensaje): ?>
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
 
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" id="usuario" name="usuario"
                               placeholder="Tu usuario"
                               value="<?= htmlspecialchars($usuario) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password"
                               placeholder="Tu contraseña" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
            </form>
        </div>
    </div>
</body>
</html>