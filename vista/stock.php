<?php
// ============================================================
//  vista/stock.php  — Solo presentación
//  La lógica está en controlador/stockController.php
// ============================================================
session_start();
 
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
 
require_once __DIR__ . '/../controlador/stockController.php';
 
$ctrl            = new stockController();
$categoria_activa = $_GET['categoria'] ?? 'todos';
$productos       = $ctrl->obtenerVitrina($categoria_activa);
 
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Vitrina de Stock — Pastelería IP</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/home.css">
    <link rel="stylesheet" href="../activos/css/stock.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="home.php" class="logo">
                <i class="fas fa-birthday-cake"></i> Vitrina de Stock — Pastelería IP
            </a>
            <div style="display:flex;gap:12px;align-items:center;">
                <a href="home.php" style="color:rgba(255,255,255,.6);font-size:13px;"><i class="fas fa-arrow-left"></i> Volver</a>
                <a href="logout.php" style="color:rgba(255,255,255,.6);font-size:13px;"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </header>
 
    <main class="container page-content">
        <nav class="filtros">
            <?php
            $cats = ['todos'=>'Ver Todo','tortas'=>'Tortas','postres'=>'Postres','bocaditos'=>'Bocaditos'];
            foreach ($cats as $key => $label):
            ?>
                <a href="stock.php?categoria=<?= $key ?>"
                   class="btn-filtro <?= $categoria_activa === $key ? 'activo' : '' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </nav>
 
        <div class="gallery-grid">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $item): ?>
                    <article class="gallery-item">
                        <div class="image-wrapper">
                            <img src="<?= e($item['imagen']) ?>" alt="<?= e($item['nombre']) ?>" loading="lazy">
                        </div>
                        <div class="item-info">
                            <h2 class="item-name"><?= e($item['nombre']) ?></h2>
                            <div class="stock-badge">
                                <i class="fas fa-box"></i> <?= (int)$item['stock'] ?> und.
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No hay productos disponibles en esta categoría.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
 
    <footer class="footer">
        <p>Sistema de Vitrina &copy; <?= date('Y') ?> — Pastelería IP</p>
    </footer>
</body>
</html>