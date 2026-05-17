<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../config/database.php';

$servicios = [];
if ($pdo) {
    try {
        $stmt = $pdo->query(
            "SELECT nombre_servicio, descripcion, precio_base
             FROM servicios WHERE estado_disponible = 1
             ORDER BY RAND() LIMIT 6"
        );
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());
    }
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function money($v) { return 'S/. ' . number_format((float)$v, 2); }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inicio — Pastelería IP</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/home.css">
</head>
<body>
    <a class="skip-link" href="#main">Saltar al contenido</a>

    <header class="site-header">
        <div class="container header-grid">
            <a class="logo-link" href="home.php">
                <div style="width:36px;height:36px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <span class="brand">Pastelería IP</span>
            </a>
            <nav class="main-nav" aria-label="Navegación principal">
                <ul>
                    <li><a href="home.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="pedidos.php"><i class="fas fa-shopping-bag"></i> Pedidos</a></li>
                    <li><a href="pedidosPersonalizados.php"><i class="fas fa-magic"></i> Personalizado</a></li>
                    <li><a href="catalogo.php"><i class="fas fa-store"></i> Catalogo</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <a href="../admin/vista/dashboard.php" class="btn btn-ghost" style="font-size:13px;">
                        <i class="fas fa-cog"></i> Admin
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </header>

    <main id="main">
        <section class="hero container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1>Sabores que enamoran, pasteles que inspiran</h1>
                    <p>Hola, <strong><?= e($_SESSION['nombre']) ?></strong>. Encuentra nuestras creaciones artesanales, perfectas para cualquier ocasión.</p>
                    <div class="hero-cta">
                        <a class="btn btn-primary" href="pedidos.php"><i class="fas fa-shopping-bag"></i> Hacer Pedido</a>
                        <a class="btn btn-ghost" href="pedidosPersonalizados.php"><i class="fas fa-magic"></i> Personalizar</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-card">
                        <div class="badge">⭐ Lo más vendido</div>
                        <img src="https://cdn.blog.paulinacocina.net/wp-content/uploads/2025/06/pastel-tres-leches-facil-1749719712.jpg" alt="Torta Tres Leches">
                    </div>
                </div>
            </div>

            <!-- Agenda rápida -->
            <div class="agenda-rapida">
                <div class="agenda-left">
                    <h3><i class="fas fa-bolt"></i> ¿Necesitas algo rápido?</h3>
                    <p>Registra tu pedido y lo preparamos con prioridad.</p>
                </div>
                <div class="agenda-right">
                    <a class="btn btn-primary" href="pedidos.php">Hacer pedido ahora</a>
                </div>
            </div>
        </section>

        <!-- Servicios destacados -->
        <section id="servicios" class="container" style="padding-bottom:48px;">
            <h2 style="font-family:'Playfair Display',serif;font-size:28px;color:var(--primary-dark);margin-bottom:8px;">
                Nuestros Servicios
            </h2>
            <p style="color:var(--text-muted);margin-bottom:24px;">Elige entre nuestras creaciones artesanales</p>
            <div class="servicios-grid">
                <?php foreach ($servicios as $s): ?>
                    <div class="servicio-card">
                        <h3><?= e($s['nombre_servicio']) ?></h3>
                        <p><?= e($s['descripcion']) ?></p>
                        <div class="precio-tag"><?= money($s['precio_base']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align:center;margin-top:24px;">
                <a href="pedidos.php" class="btn btn-primary">Ver todos y hacer pedido</a>
            </div>
        </section>

        <!-- Sobre nosotros -->
        <section id="nosotros" class="container">
            <div class="bloque-info">
                <h2>Sobre Pastelería IP</h2>
                <p>Somos una pastelería artesanal ubicada en Pucallpa, Perú. Cada creación está hecha con amor y los mejores ingredientes. Aceptamos pedidos personalizados para cumpleaños, bodas y todo tipo de eventos especiales.</p>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <h4>IP — Pastelería</h4>
                <p>Creaciones artesanales con amor desde Pucallpa.</p>
            </div>
            <div>
                <h4>Contacto</h4>
                <p>Av. Principal #123 · Pucallpa, Perú<br>contacto@pasteleriaip.com</p>
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
        <div class="site-copyright">&copy; <?= date('Y') ?> IP — Pastelería. Todos los derechos reservados.</div>
    </footer>
</body>
</html>