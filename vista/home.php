<?php
session_start();

// Si la variable de sesión no existe, significa que no se ha logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

// Importar la conexión PDO a la base de datos
require_once __DIR__ . '/../config/database.php';

// Cargar servicios desde la base de datos
$Pedidos = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT id_servicio as id_pedidos, nombre_servicio as nombre, descripcion, precio_base as precio FROM servicios WHERE estado_disponible = 1");
        $Pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error cargando pedidos: ' . $e->getMessage());
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
        <meta name="author" content="Augusto IP">
        <meta name="robots" content="index, follow">
        <meta name="description" content="Pastelería IP - Pedidos y creaciones">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../activos/css/home.css">
        <link rel="icon" href="../activos/img/logo.png">
        <title>IP - Pasteleria</title>
    </head>
    <body>
        <a class="skip-link" href="#main">Saltar al contenido</a>

        <header class="site-header">
            <div class="container header-grid">
                <a class="logo-link" href="home.php">
                    <img src="../img/logoPrincipal.png" alt="Pasteleria-IP">
                    <span class="brand">Pastelería-IP</span>
                </a>

                <nav class="main-nav" aria-label="Navegación principal">
                    <ul>
                        <li><a href="#servicios"><i class="fas fa-birthday-cake"></i> Servicios</a></li>
                        <li><a href="pedidos.php"><i class="fas fa-shopping-bag"></i> Pedidos</a></li>
                        <li><a href="pedidosPersonalizados.php"><i class="fas fa-cookie"></i> Personalizados</a></li>
                        <li><a href="#nosotros"><i class="fas fa-info-circle"></i> Nosotros</a></li>
                    </ul>
                </nav>

                <div>
                    <a href="stock.php" class="btn btn-primary">Ver Stock</a>
                </div>
            </div>
        </header>

        <section class="hero container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1>Sabores que enamoran, pasteles que inspiran</h1>
                    <p>Encuentra nuestras creaciones artesanales, perfectas para cualquier ocasión. Pedidos rápidos y entregas confiables.</p>
                    <div class="hero-cta">
                        <a class="btn btn-primary" href="pedidos.php">Pedidos</a>
                        <a class="btn btn-ghost" href="pedidosPersonalizados.php">Pedidos Personalizar</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-card">
                        <div class="badge">Lo más vendido</div>
                        <img src="https://cdn.blog.paulinacocina.net/wp-content/uploads/2025/06/pastel-tres-leches-facil-1749719712.jpg" alt="Pastel destacado Tres Leches">
                    </div>
                </div>
            </div>


            <section class="container">
                <div class="agenda-rapida">
                    <div class="agenda-left">
                        <h3>¿Necesitas algo rápido?</h3>
                        <p>Registra tu pedido o cita en línea y lo prepararemos con prioridad.</p>
                    </div>
                    <div class="agenda-right">
                        <a class="btn btn-primary" href="agendarCita.php">Registrar cita</a>
                    </div>
                </div>
            </section>

            <section id="nosotros" class="container bloque-info">
                <h2>Sobre el proyecto</h2>
                <p>Este prototipo está optimizado para XAMPP y despliegue en hosting PHP. Diseñado con prioridad en usabilidad y accesibilidad.</p>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-grid">
                <div>
                    <h4>IP - Pastelería</h4>
                    <p>Creaciones artesanales con amor.</p>
                </div>
                <div>
                    <h4>Contacto</h4>
                    <p>Av. Principal #123 • Pucallpa, Perú • contacto@pasteleriaip.com</p>
                </div>
                <div>
                    <h4>Síguenos</h4>
                    <div class="socials">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <div class="site-copyright">&copy; <?= date('Y') ?> IP - Pastelería. Todos los derechos reservados.</div>
        </footer>
    </body>
</html>