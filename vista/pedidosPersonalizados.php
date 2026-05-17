<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Procesar formulario
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_pedido = $_POST['nombre_pedido'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tamaño = $_POST['tamaño'] ?? '';
    $presupuesto = $_POST['presupuesto'] ?? '';
    $fecha_entrega = $_POST['fecha_entrega'] ?? '';
    $personas = (int)($_POST['personas'] ?? 0);
    $comentarios = $_POST['comentarios'] ?? '';

    // Validar archivo
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_type = $_FILES['foto']['type'];
        $file_size = $_FILES['foto']['size'];

        // Validar tipo de archivo
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file_type, $allowed_types)) {
            $mensaje = '❌ Solo se permiten imágenes (JPG, PNG, GIF, WEBP).';
            $tipo_mensaje = 'error';
        }
        // Validar tamaño (máx 5MB)
        elseif ($file_size > 5 * 1024 * 1024) {
            $mensaje = '❌ La imagen no debe superar 5MB.';
            $tipo_mensaje = 'error';
        }
        else {
            // Generar nombre único
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_filename = 'pedido_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_dir = __DIR__ . '/../activos/uploads/';
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $foto_path = $new_filename;
            } else {
                $mensaje = '❌ Error al subir la imagen.';
                $tipo_mensaje = 'error';
            }
        }
    }

    // Si no hay errores, guardar en BD
    if (empty($mensaje) && !empty($nombre_pedido) && !empty($descripcion) && !empty($tamaño) && !empty($presupuesto) && !empty($fecha_entrega)) {
        try {
            $id_usuario = (int)$_SESSION['id_usuario'];
            $estado = 'pendiente_revision';
            $fecha_pedido = date('Y-m-d');

            $stmt = $pdo->prepare("
                INSERT INTO pedidos_personalizados (id_usuario, nombre_pedido, descripcion, tamaño, presupuesto, fecha_pedido, fecha_entrega, cantidad_personas, foto, comentarios, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $id_usuario,
                $nombre_pedido,
                $descripcion,
                $tamaño,
                (float)$presupuesto,
                $fecha_pedido,
                $fecha_entrega,
                $personas,
                $foto_path,
                $comentarios,
                $estado
            ]);

            if ($result) {
                $mensaje = '✅ Pedido personalizado registrado. Nos contactaremos para confirmar detalles.';
                $tipo_mensaje = 'exito';
            }
        } catch (Exception $e) {
            $mensaje = '❌ Error al registrar pedido: ' . $e->getMessage();
            $tipo_mensaje = 'error';
            error_log('Error pedido personalizado: ' . $e->getMessage());
        }
    } elseif (empty($mensaje)) {
        $mensaje = '⚠️ Por favor completa todos los campos obligatorios.';
        $tipo_mensaje = 'advertencia';
    }
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Pedidos Personalizados - Pastelería IP">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/home.css">
    <style>
        .form-container {
            max-width: 900px;
            margin: 40px auto;
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: var(--shadow-md);
        }

        .form-titulo {
            font-size: 28px;
            color: var(--primary-dark);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text);
        }

        .required {
            color: #ef4444;
            font-weight: 700;
        }

        .form-select, .form-input, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s ease;
            background: var(--bg);
        }

        .form-select:focus, .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(212, 132, 92, 0.1);
            background: white;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: linear-gradient(135deg, rgba(249, 213, 184, 0.3) 0%, rgba(245, 240, 232, 0.5) 100%);
        }

        .upload-zone:hover {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(212, 132, 92, 0.1) 0%, rgba(245, 240, 232, 0.8) 100%);
        }

        .upload-zone.drag-over {
            border-color: var(--primary);
            background: linear-gradient(135deg, var(--accent) 0%, rgba(245, 240, 232, 0.5) 100%);
        }

        .upload-icon {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .upload-text {
            font-size: 13px;
            color: var(--text-muted);
        }

        .upload-text strong {
            color: var(--primary);
        }

        #upload-input {
            display: none;
        }

        .preview-container {
            margin-top: 12px;
            display: none;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }

        .remove-image {
            margin-top: 8px;
            padding: 8px 12px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .remove-image:hover {
            background: #fecaca;
        }

        .mensaje {
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .mensaje.exito {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .mensaje.error {
            background: #fef2f2;
            color: #7f1d1d;
            border-left: 4px solid #ef4444;
        }

        .mensaje.advertencia {
            background: #fffbeb;
            color: #78350f;
            border-left: 4px solid #f59e0b;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .breadcrumb {
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: underline;
        }

        .info-box {
            background: linear-gradient(135deg, var(--accent) 0%, rgba(249, 213, 184, 0.3) 100%);
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
            font-size: 13px;
            color: var(--text);
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
                margin: 20px auto;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-titulo {
                font-size: 20px;
            }
        }
    </style>
    <title>Pedido Personalizado - Pastelería IP</title>
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
                    <li><a href="pedidos.php"><i class="fas fa-shopping-bag"></i> Catálogo</a></li>
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
                <a href="home.php">Inicio</a> / <strong>Pedido Personalizado</strong>
            </div>

            <div class="form-container">
                <h1 class="form-titulo">
                    <i class="fas fa-magic"></i> Pedido Personalizado
                </h1>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i> <strong>¿Cómo funciona?</strong> Cuéntanos tus ideas, sube una foto de referencia y nosotros te contactaremos para confirmar detalles, diseño y presupuesto final.
                </div>

                <?php if (!empty($mensaje)): ?>
                    <div class="mensaje <?= e($tipo_mensaje) ?>">
                        <span><?= $mensaje ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" for="nombre_pedido">Nombre del pedido <span class="required">*</span></label>
                        <input type="text" id="nombre_pedido" name="nombre_pedido" placeholder="Ej: Torta de cumpleaños de Sofia" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="descripcion">Descripción y ideas <span class="required">*</span></label>
                        <textarea id="descripcion" name="descripcion" placeholder="Cuéntanos cómo imaginas tu torta o postre. Incluye sabores, colores, temas, diseños, etc..." required class="form-textarea"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-upload"></i> Sube una foto de referencia <span class="required">*</span></label>
                        <div class="upload-zone" id="upload-zone">
                            <div class="upload-icon"><i class="fas fa-image"></i></div>
                            <p class="upload-text">Arrastra una imagen aquí o <strong>haz clic para seleccionar</strong></p>
                            <p class="upload-text" style="font-size: 12px; margin-top: 4px;">JPG, PNG, GIF o WEBP • Máx 5MB</p>
                        </div>
                        <input type="file" id="upload-input" name="foto" accept="image/*" required>
                        <div class="preview-container" id="preview-container">
                            <img id="preview-image" class="preview-image" src="" alt="Preview">
                            <button type="button" class="remove-image" id="remove-image">Eliminar imagen</button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="tamaño">Tamaño aproximado <span class="required">*</span></label>
                            <select id="tamaño" name="tamaño" required class="form-select">
                                <option value="">Selecciona tamaño...</option>
                                <option value="pequeño">Pequeño (2-4 porciones)</option>
                                <option value="mediano">Mediano (6-8 porciones)</option>
                                <option value="grande">Grande (10-15 porciones)</option>
                                <option value="muy_grande">Muy Grande (15+ porciones)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="personas">Cantidad de personas <span class="required">*</span></label>
                            <input type="number" id="personas" name="personas" min="1" max="1000" placeholder="10" required class="form-input">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="presupuesto">Presupuesto aproximado (S/.) <span class="required">*</span></label>
                            <input type="number" id="presupuesto" name="presupuesto" min="10" step="0.50" placeholder="150.00" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="fecha_entrega">Fecha de entrega <span class="required">*</span></label>
                            <input type="date" id="fecha_entrega" name="fecha_entrega" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="comentarios">Comentarios adicionales</label>
                        <textarea id="comentarios" name="comentarios" placeholder="Alergias, restricciones dietéticas, instrucciones especiales..." class="form-textarea"></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Enviar Solicitud Personalizada
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

    <script>
        const uploadZone = document.getElementById('upload-zone');
        const uploadInput = document.getElementById('upload-input');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const removeButton = document.getElementById('remove-image');

        uploadZone.addEventListener('click', () => uploadInput.click());

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('drag-over');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('drag-over');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadInput.files = files;
                previewFile(files[0]);
            }
        });

        uploadInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                previewFile(e.target.files[0]);
            }
        });

        removeButton.addEventListener('click', () => {
            uploadInput.value = '';
            previewContainer.style.display = 'none';
            uploadZone.style.display = 'block';
        });

        function previewFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
                uploadZone.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    </script>
</body>
</html>
