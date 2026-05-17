<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Cargar servicios disponibles
$servicios = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT id_servicio, nombre_servicio, descripcion, precio_base FROM servicios WHERE estado_disponible = 1 ORDER BY nombre_servicio");
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error cargando servicios: ' . $e->getMessage());
    }
}

// Procesar formulario
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_servicio = (int)($_POST['id_servicio'] ?? 0);
    $cantidad = (int)($_POST['cantidad'] ?? 1);
    $fecha_entrega = $_POST['fecha_entrega'] ?? '';
    $notas = $_POST['notas'] ?? '';

    if ($id_servicio > 0 && $cantidad > 0 && !empty($fecha_entrega)) {
        try {
            $id_usuario = (int)$_SESSION['id_usuario'];
            $estado = 'pendiente';
            $fecha_pedido = date('Y-m-d');
            
            $stmt = $pdo->prepare("
                INSERT INTO pedidos (id_usuario, id_servicio, cantidad, fecha_pedido, fecha_entrega, notas, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$id_usuario, $id_servicio, $cantidad, $fecha_pedido, $fecha_entrega, $notas, $estado]);
            
            if ($result) {
                $mensaje = '✅ Pedido registrado exitosamente. Nos contactaremos pronto.';
                $tipo_mensaje = 'exito';
            }
        } catch (Exception $e) {
            $mensaje = '❌ Error al registrar pedido: ' . $e->getMessage();
            $tipo_mensaje = 'error';
            error_log('Error pedido: ' . $e->getMessage());
        }
    } else {
        $mensaje = '⚠️ Por favor completa todos los campos.';
        $tipo_mensaje = 'advertencia';
    }
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function money($value) {
    return 'S/. ' . number_format((float)$value, 2);
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Realizar pedidos - Pastelería IP">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/home.css">
    <link rel="stylesheet" href="../activos/css/pedidos.css">
    <title>Hacer Pedido - Pastelería IP</title>
</head>
<body>
    <a class="skip-link" href="#main">Saltar al contenido</a>

    <header class="site-header">
        <div class="container header-grid">
            <a class="logo-link" href="home.php">
                <img src="../activos/img/logo.png" alt="IP - Pasteleria">
                <span class="brand">IP - Pastelería</span>
            </a>
            <nav class="main-nav">
                <ul>
                    <li><a href="home.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="pedidos.php"><i class="fas fa-shopping-bag"></i> Pedidos</a></li>
                    <li><a href="pedidosPersonalizados.php"><i class="fas fa-cookie"></i> Personalizado</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a class="btn btn-outline" href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </header>

    <main id="main">
        <div class="container">
            <div class="breadcrumb">
                <a href="home.php">Inicio</a> / <strong>Hacer Pedido</strong>
            </div>

            <div class="form-container">
                <h1 class="form-titulo">
                    <i class="fas fa-shopping-bag"></i> Hacer Pedido
                </h1>

                <?php if (!empty($mensaje)): ?>
                    <div class="mensaje <?= e($tipo_mensaje) ?>">
                        <span><?= $mensaje ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-birthday-cake"></i> Selecciona un servicio o producto</label>
                        <div class="catalogo-grid">
                            <?php foreach ($servicios as $servicio): ?>
                                <label class="item-catalogo">
                                    <input type="radio" name="id_servicio" value="<?= (int)$servicio['id_servicio'] ?>" required>
                                    <div class="catalogo-content">
                                        <h4><?= e($servicio['nombre_servicio']) ?></h4>
                                        <p><?= e($servicio['descripcion']) ?></p>
                                        <div class="catalogo-precio"><?= money($servicio['precio_base']) ?></div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="cantidad">Cantidad</label>
                            <input type="number" id="cantidad" name="cantidad" min="1" value="1" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="fecha_entrega">Fecha de entrega</label>
                            <input type="date" id="fecha_entrega" name="fecha_entrega" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notas">Notas especiales</label>
                        <textarea id="notas" name="notas" placeholder="Ej: Sin azúcar, sin frutos secos, decoración especial..." class="form-textarea"></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check-circle"></i> Confirmar Pedido
                    </button>
                </form>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <h4>IP - Pastelería</h4>
                <p>Creaciones artesanales con amor.</p>
            </div>
            <div>
                <h4>Contacto</h4>
                <p>Av. Principal #123 • (555) 987-6543 • contacto@ip.com</p>
            </div>
            <div>
                <h4>Síguenos</h4>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="site-copyright">&copy; <?= date('Y') ?> IP - Pastelería. Todos los derechos reservados.</div>
    </footer>
</body>
</html>
