<?php
session_start();

// 1. Detectamos la raíz real de tu proyecto en XAMPP (C:\xampp\htdocs\Pasteleria)
$root = $_SERVER['DOCUMENT_ROOT'] . '/Pasteleria';

// 2. Traemos la conexión y el controlador usando rutas fijas y seguras
require_once $root . '/config/database.php';

// OJO: Aquí revisa cómo se llama tu carpeta en Windows. 
// Si se llama "controlador" en minúscula, ponlo en minúscula. Si es "Controlador", déjalo así.
require_once $root . '/../controlador/stockController.php';

// 3. Por si acaso el controlador no cargó la variable, la aseguramos aquí
if (!isset($categoria_activa)) {
    $categoria_activa = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';
}

function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitrina de Stock en Vivo - Pastelería IP</title>
    <!-- Tipografía y Iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Hoja de Estilos Externa -->
    <link rel="stylesheet" href="../activos/css/stock.css">
</head>
<body>

    <header class="navbar">
        <div class="container nav-container">
            <a href="stock.php" class="logo">
                <i class="fas fa-snowflake"></i> Vitrina de Stock Diario
            </a>
            <span class="nav-note">Monitoreo de productos al contado inmediato</span>
        </div>
    </header>

    <main class="container page-content">
        <!-- Filtros Rápidos de Mostrador -->
        <nav class="filtros" aria-label="Categorías de productos">
            <a href="stock.php?categoria=todos" class="btn-filtro <?= $categoria_activa === 'todos' ? 'activo' : '' ?>">Ver Todo</a>
            <a href="stock.php?categoria=tortas" class="btn-filtro <?= $categoria_activa === 'tortas' ? 'activo' : '' ?>">Tortas</a>
            <a href="stock.php?categoria=postres" class="btn-filtro <?= $categoria_activa === 'postres' ? 'activo' : '' ?>">Postres</a>
            <a href="stock.php?categoria=bocaditos" class="btn-filtro <?= $categoria_activa === 'bocaditos' ? 'activo' : '' ?>">Bocaditos</a>
        </nav>

        <!-- Cuadrícula Dinámica de la Vitrina -->
        <div class="gallery-grid">
            <?php if (!empty($productos_vitrina)): ?>
                <?php foreach ($productos_vitrina as $item): ?>
                    <article class="gallery-item">
                        <div class="image-wrapper">
                            <img src="<?= e($item['imagen']) ?>" alt="<?= e($item['nombre']) ?>" loading="lazy">
                        </div>
                        <div class="item-info">
                            <h2 class="item-name"><?= e($item['nombre']) ?></h2>
                            <div class="stock-badge">
                                <i class="fas fa-box"></i> Quedan: <?= (int)$item['stock'] ?> und.
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Estado que se muestra si la congeladora está vacía o el stock llegó a 0 -->
                <div class="empty-state">
                    <i class="fas fa-chart-pie"></i>
                    <p>No hay productos con stock registrado para esta categoría en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <p>Sistema Interno de Control de Vitrina &copy; <?= date('Y') ?> - Pastelería IP</p>
    </footer>

</body>
</html>