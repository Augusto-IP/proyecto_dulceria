<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../controlador/pedidosPersonalizadosController.php';

$ctrl = new pedidosPersonalizadosController();
$misPedidos = $ctrl->obtenerMisPedidos($_SESSION['id_usuario']);

$mensaje = '';
$tipo_mensaje = '';

// Procesar nuevo pedido personalizado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_pedido   = trim($_POST['nombre_pedido']   ?? '');
    $descripcion     = trim($_POST['descripcion']     ?? '');
    $tamaño          = $_POST['tamaño']              ?? '';
    $presupuesto     = (float)($_POST['presupuesto']  ?? 0);
    $fecha_entrega   = $_POST['fecha_entrega']        ?? '';
    $cantidad_personas = (int)($_POST['personas']     ?? 1);
    $comentarios     = trim($_POST['comentarios']     ?? '');
    $foto_path       = null;

    // Procesamiento de la Imagen
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = time() . '_' . basename($_FILES['foto']['name']);
        $ruta_destino = __DIR__ . '/../activos/imagenes/pedidos/' . $nombre_archivo;
        
        if (!is_dir(dirname($ruta_destino))) {
            mkdir(dirname($ruta_destino), 0777, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            $foto_path = 'activos/imagenes/pedidos/' . $nombre_archivo;
        }
    }

    if ($nombre_pedido && $descripcion && $tamaño && $presupuesto > 0 && $fecha_entrega) {
        if (strtotime($fecha_entrega) >= strtotime(date('Y-m-d', strtotime('+2 days')))) {
            try {
                $ok = $ctrl->registrar(
                    $_SESSION['id_usuario'],
                    $nombre_pedido,
                    $descripcion,
                    $tamaño,
                    $presupuesto,
                    $fecha_entrega,
                    $cantidad_personas,
                    $foto_path,
                    $comentarios
                );
                
                if ($ok) {
                    $mensaje = '✅ Pedido personalizado registrado correctamente. Te contactaremos pronto.';
                    $tipo_mensaje = 'exito';
                    $misPedidos = $ctrl->obtenerMisPedidos($_SESSION['id_usuario']);
                } else {
                    $mensaje = '❌ Error al registrar el pedido. Intenta de nuevo.';
                    $tipo_mensaje = 'error';
                }
            } catch (Exception $e) {
                $mensaje = '❌ Error al registrar: ' . $e->getMessage();
                $tipo_mensaje = 'error';
                error_log('Error pedido personalizado: ' . $e->getMessage());
            }
        } else {
            $mensaje = '⚠️ La fecha de entrega debe ser con al menos 2 días de anticipación.';
            $tipo_mensaje = 'advertencia';
        }
    } else {
        $mensaje = '⚠️ Completa todos los campos requeridos.';
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
    <title>Pedido Personalizado — Pastelería IP</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/home.css">
    <!-- Aquí vinculamos tu nuevo archivo externo de estilos -->
    <link rel="stylesheet" href="../activos/css/pedidosPersonalizados.css">
</head>
<body>

    <header class="navbar">
        <div class="container nav-box">
            <a href="home.php" class="brand">
                <i class="fas fa-birthday-cake"></i> Pastelería IP
            </a>
            <nav class="nav-links">
                <a href="home.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="pedidos.php"><i class="fas fa-shopping-bag"></i> Pedidos</a>
                <a href="catalogo.php"><i class="fas fa-store"></i> Catálogo</a>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </nav>
        </div>
    </header>

    <main class="container main-content">
        
        <div class="breadcrumb">
            <a href="home.php">Inicio</a> / <strong>Pedido Personalizado</strong>
        </div>

        <!-- Alertas del Sistema -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= e($tipo_mensaje) ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <!-- Sección del Formulario -->
        <section class="card-section">
            <h1 class="title-page"><i class="fas fa-magic"></i> Diseña tu Pedido</h1>
            <p class="subtitle-page">Cuéntanos tu idea, sube una foto de referencia y nos contactaremos contigo para coordinar los detalles finales.</p>

            <form method="POST" enctype="multipart/form-data" class="form-style">
                
                <div class="form-group">
                    <label class="form-label">Nombre del pedido <span class="required">*</span></label>
                    <input type="text" name="nombre_pedido" required class="form-input" placeholder="Ej: Torta Temática de Cumpleaños">
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción detallada e ideas <span class="required">*</span></label>
                    <textarea name="descripcion" required class="form-textarea" rows="4" placeholder="Especifica sabores, colores, decoraciones o temáticas que desees..."></textarea>
                </div>

                <!-- Zona de Imagen (Drag & Drop) -->
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-image"></i> Imagen de Referencia (Opcional)</label>
                    <div id="upload-zone" class="upload-box">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p class="upload-text">Arrastra una imagen o <strong class="highlight">haz clic aquí</strong></p>
                        <p class="upload-subtext">JPG, PNG o WEBP (Máx. 5MB)</p>
                    </div>
                    <input type="file" id="upload-input" name="foto" accept="image/*" class="hide-input">
                    
                    <div id="preview-container" class="preview-box">
                        <img id="preview-image" src="" alt="Preview" class="preview-img">
                        <br>
                        <button type="button" id="remove-image" class="btn-remove-img">
                            <i class="fas fa-trash"></i> Quitar imagen
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tamaño <span class="required">*</span></label>
                        <select name="tamaño" required class="form-select">
                            <option value="">Selecciona...</option>
                            <option value="pequeño">Pequeño (2–4 porciones)</option>
                            <option value="mediano">Mediano (6–8 porciones)</option>
                            <option value="grande">Grande (10–15 porciones)</option>
                            <option value="muy_grande">Muy Grande (15+ porciones)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cantidad de personas <span class="required">*</span></label>
                        <input type="number" name="personas" min="1" value="1" required class="form-input">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Presupuesto estimado (S/.) <span class="required">*</span></label>
                        <input type="number" name="presupuesto" min="10" step="0.50" placeholder="150.00" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de entrega <span class="required">*</span></label>
                        <input type="date" name="fecha_entrega" required class="form-input" min="<?= date('Y-m-d', strtotime('+2 days')) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Comentarios adicionales</label>
                    <textarea name="comentarios" class="form-textarea" rows="2" placeholder="Alergias, restricciones alimentarias o notas específicas..."></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Enviar Solicitud de Pedido
                </button>
            </form>
        </section>

        <!-- Sección de Listado de Pedidos Existentes -->
        <section class="card-section">
            <h2 class="history-title"><i class="fas fa-list"></i> Historial de Solicitudes (<?= count($misPedidos) ?>)</h2>
            
            <?php if (count($misPedidos) === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Aún no has registrado solicitudes de pedidos personalizados.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table-style">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Pedido</th>
                                <th>Tamaño</th>
                                <th>Presupuesto</th>
                                <th>Fecha Entrega</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($misPedidos as $p): ?>
                                <tr>
                                    <td>#<?= e($p['id_pedido_per'] ?? $p['id']) ?></td>
                                    <td><strong><?= e($p['nombre_pedido']) ?></strong></td>
                                    <td><?= ucfirst(e($p['tamaño'])) ?></td>
                                    <td class="table-price"><?= money($p['presupuesto']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_entrega'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= str_replace('_', '-', $p['estado']) ?>">
                                            <?= ucfirst(str_replace('_', ' ', $p['estado'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
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

    <script>
    const zone    = document.getElementById('upload-zone');
    const input   = document.getElementById('upload-input');
    const preview = document.getElementById('preview-container');
    const img     = document.getElementById('preview-image');
    const remove  = document.getElementById('remove-image');

    zone.addEventListener('click', () => input.click());
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => { zone.classList.remove('dragover'); });
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files[0]) showPreview(e.dataTransfer.files[0]);
    });
    input.addEventListener('change', e => { if (e.target.files[0]) showPreview(e.target.files[0]); });
    remove.addEventListener('click', () => {
        input.value = '';
        preview.style.display = 'none';
        zone.style.display = 'block';
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            preview.style.display = 'block';
            zone.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
    </script>
</body>
</html>