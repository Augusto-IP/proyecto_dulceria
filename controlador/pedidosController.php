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