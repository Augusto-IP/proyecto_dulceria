<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../controlador/stockController.php';

$ctrl = new stockController();
$categoria_activa = $_GET['categoria'] ?? 'todos';
$productos = $ctrl->obtenerVitrina($categoria_activa);

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Catalogo - Pastelería IP</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/home.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #d4526e 0%, #c0435d 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo i {
            font-size: 20px;
        }
        .nav-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 13px;
            margin-left: 15px;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: white;
        }
        .page-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .categories {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .cat-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-block;
        }
        .cat-btn:hover,
        .cat-btn.active {
            background: #d4526e;
            color: white;
            border-color: #d4526e;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .product-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            background: white;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            height: 180px;
            background: #f5f5f5;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-info {
            padding: 15px;
        }
        .product-name {
            font-weight: 600;
            color: #333;
            margin: 0 0 8px;
            font-size: 15px;
        }
        .product-category {
            font-size: 12px;
            color: #999;
            margin-bottom: 8px;
        }
        .product-price {
            color: #d4526e;
            font-weight: 700;
            font-size: 18px;
        }
        .stock-badge {
            display: inline-block;
            background: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 8px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <a href="home.php" class="logo">
                <i class="fas fa-birthday-cake"></i>
                Catalogo
            </a>
            <div class="nav-links">
                <a href="pedidos.php">Pedidos</a>
                <a href="pedidosPersonalizados.php">Personalizados</a>
                <a href="home.php">Volver</a>
                <a href="logout.php">Salir</a>
            </div>
        </div>
    </header>

    <main class="page-content">
        <h1 style="margin-top: 0; color: #333; font-size: 28px;">Nuestros Productos</h1>
        
        <div class="categories">
            <a href="?categoria=todos" class="cat-btn <?= $categoria_activa === 'todos' ? 'active' : '' ?>">Ver Todo</a>
            <a href="?categoria=tortas" class="cat-btn <?= $categoria_activa === 'tortas' ? 'active' : '' ?>">Tortas</a>
            <a href="?categoria=postres" class="cat-btn <?= $categoria_activa === 'postres' ? 'active' : '' ?>">Postres</a>
            <a href="?categoria=bocaditos" class="cat-btn <?= $categoria_activa === 'bocaditos' ? 'active' : '' ?>">Bocaditos</a>
        </div>

        <?php if (count($productos) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No hay productos disponibles en esta categoria</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($productos as $prod): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($prod['imagen'])): ?>
                                <img src="<?= e($prod['imagen']) ?>" alt="<?= e($prod['nombre']) ?>">
                            <?php else: ?>
                                <i class="fas fa-image" style="font-size: 48px; color: #ddd;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= e($prod['nombre']) ?></h3>
                            <div class="product-category"><?= ucfirst(e($prod['categoria'])) ?></div>
                            <div class="product-price">S/. <?= number_format($prod['precio'], 2) ?></div>
                            <div class="stock-badge">Stock: <?= $prod['stock'] ?> unidades</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
