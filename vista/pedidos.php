<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../controlador/pedidosController.php';

$ctrl       = new pedidosController();
$servicios  = $ctrl->obtenerServicios();
$misPedidos = $ctrl->obtenerMisPedidos($_SESSION['id_usuario']);

$mensaje      = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'nuevo') {
    $id_servicio   = (int)($_POST['id_servicio']   ?? 0);
    $cantidad      = (int)($_POST['cantidad']       ?? 1);
    $fecha_entrega = $_POST['fecha_entrega']        ?? '';
    $notas         = $_POST['notas']                ?? '';

    if ($id_servicio > 0 && $cantidad > 0 && !empty($fecha_entrega)) {
        if (strtotime($fecha_entrega) > strtotime(date('Y-m-d'))) {
            $ok = $ctrl->registrar($_SESSION['id_usuario'], $id_servicio, $cantidad, $fecha_entrega, $notas);
            if ($ok) {
                $mensaje      = 'Pedido registrado correctamente.';
                $tipo_mensaje = 'exito';
                $misPedidos   = $ctrl->obtenerMisPedidos($_SESSION['id_usuario']);
            } else {
                $mensaje      = 'Error al registrar el pedido.';
                $tipo_mensaje = 'error';
            }
        } else {
            $mensaje      = 'La fecha de entrega debe ser posterior a hoy.';
            $tipo_mensaje = 'advertencia';
        }
    } else {
        $mensaje      = 'Completa todos los campos requeridos.';
        $tipo_mensaje = 'advertencia';
    }
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function money($v) { return 'S/. ' . number_format((float)$v, 2); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pedidos — Pastelería IP</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/home.css">
    <link rel="stylesheet" href="../activos/css/pedidos.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="home.php" class="logo">
                <i class="fas fa-shopping-bag"></i> Mis Pedidos
            </a>
            <div style="display:flex;gap:12px;align-items:center;">
                <a href="pedidosPersonalizados.php" style="color:rgba(255,255,255,.6);font-size:13px;">Personalizados</a>
                <a href="catalogo.php" style="color:rgba(255,255,255,.6);font-size:13px;">Catalogo</a>
                <a href="home.php" style="color:rgba(255,255,255,.6);font-size:13px;">Volver</a>
                <a href="logout.php" style="color:rgba(255,255,255,.6);font-size:13px;">Salir</a>
            </div>
        </div>
    </header>

    <main class="container page-content" style="padding:40px 0;">
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= e($tipo_mensaje) ?>" style="margin-bottom:20px;">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <section style="margin-bottom:40px;">
            <h2 style="margin-bottom:20px;"><i class="fas fa-plus-circle"></i> Nuevo Pedido</h2>
            
            <form method="POST" class="form-pedido" style="background:#f9f9f9;padding:20px;border-radius:8px;">
                <input type="hidden" name="accion" value="nuevo">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Servicio <span style="color:red;">*</span></label>
                        <select name="id_servicio" required>
                            <option value="">-- Selecciona un servicio --</option>
                            <?php foreach ($servicios as $srv): ?>
                                <option value="<?= $srv['id_servicio'] ?>">
                                    <?= e($srv['nombre_servicio']) ?> — <?= money($srv['precio_base']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cantidad <span style="color:red;">*</span></label>
                        <input type="number" name="cantidad" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Entrega <span style="color:red;">*</span></label>
                        <input type="date" name="fecha_entrega" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Notas (opcional)</label>
                    <textarea name="notas" rows="3" placeholder="Especificaciones especiales, sabores, decoraciones..."></textarea>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-check"></i> Registrar Pedido
                </button>
            </form>
        </section>

        <section>
            <h2 style="margin-bottom:20px;"><i class="fas fa-list"></i> Mis Pedidos (<?= count($misPedidos) ?>)</h2>
            
            <?php if (count($misPedidos) === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox" style="font-size:48px;color:#ccc;margin-bottom:10px;display:block;"></i>
                    <p>Aún no tienes pedidos. ¡Crea uno ahora!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Servicio</th>
                                <th>Cantidad</th>
                                <th>Fecha Entrega</th>
                                <th>Estado</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($misPedidos as $p): ?>
                                <tr>
                                    <td>#<?= $p['id_pedido'] ?></td>
                                    <td><?= e($p['nombre_servicio']) ?></td>
                                    <td><?= $p['cantidad'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_entrega'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $p['estado'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $p['estado'])) ?>
                                        </span>
                                    </td>
                                    <td><?= money($p['precio_base'] * $p['cantidad']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <style>
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input, .form-group select, .form-group textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
        }
        .btn-primary {
            background: #d4526e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #c0435d;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-exito {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-advertencia {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background: #f9f9f9;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .table tr:hover {
            background: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-pendiente {
            background: #fff3cd;
            color: #856404;
        }
        .badge-en_proceso {
            background: #cfe2ff;
            color: #084298;
        }
        .badge-listo {
            background: #d1e7dd;
            color: #0f5132;
        }
        .badge-entregado {
            background: #d4edda;
            color: #155724;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>
                        <div class="catalogo-grid">
                            <?php foreach ($servicios as $srv): ?>
                                <label class="item-catalogo">
                                    <input type="radio" name="id_servicio" value="<?= (int)$srv['id_servicio'] ?>" required>
                                    <div class="catalogo-content">
                                        <h4><?= e($srv['nombre_servicio']) ?></h4>
                                        <p><?= e($srv['descripcion']) ?></p>
                                        <div class="catalogo-precio"><?= money($srv['precio_base']) ?></div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
 
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="cantidad">Cantidad <span class="required">*</span></label>
                            <input type="number" id="cantidad" name="cantidad" min="1" value="1" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="fecha_entrega">Fecha de entrega <span class="required">*</span></label>
                            <input type="date" id="fecha_entrega" name="fecha_entrega" required class="form-input"
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                    </div>
 
                    <div class="form-group">
                        <label class="form-label" for="notas">Notas especiales</label>
                        <textarea id="notas" name="notas" class="form-textarea"
                                  placeholder="Ej: Sin azúcar, decoración especial, dedicatoria..."></textarea>
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
            <div><h4>IP — Pastelería</h4><p>Creaciones artesanales con amor.</p></div>
            <div><h4>Contacto</h4><p>Av. Principal #123 · Pucallpa, Perú</p></div>
            <div>
                <h4>Síguenos</h4>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="site-copyright">&copy; <?= date('Y') ?> IP — Pastelería.</div>
    </footer>
</body>
</html>