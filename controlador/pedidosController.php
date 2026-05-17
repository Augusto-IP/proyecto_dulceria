<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/Pedidos.php';

class pedidosController {

    private $modelo;

    public function __construct() {
        global $pdo;
        $this->modelo = new Pedidos($pdo);
    }

    public function obtenerServicios() {
        global $pdo;
        if (!$pdo) return [];
        try {
            $stmt = $pdo->query(
                "SELECT id_servicio, nombre_servicio, descripcion, precio_base
                 FROM servicios WHERE estado_disponible = 1
                 ORDER BY nombre_servicio"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerMisPedidos($id_usuario) {
        return $this->modelo->obtenerPorUsuario($id_usuario);
    }

    public function obtenerTodos() {
        return $this->modelo->obtenerTodos();
    }

    public function obtenerPorId($id_pedido) {
        return $this->modelo->obtenerPorId($id_pedido);
    }

    public function registrar($id_usuario, $id_servicio, $cantidad, $fecha_entrega, $notas = '') {
        return $this->modelo->crear($id_usuario, $id_servicio, $cantidad, $fecha_entrega, $notas);
    }

    public function actualizar($id_pedido, $cantidad, $fecha_entrega, $notas = '') {
        return $this->modelo->actualizar($id_pedido, $cantidad, $fecha_entrega, $notas);
    }

    public function cambiarEstado($id_pedido, $estado) {
        return $this->modelo->cambiarEstado($id_pedido, $estado);
    }

    public function eliminar($id_pedido) {
        return $this->modelo->eliminar($id_pedido);
    }

    public function obtenerEstadisticas() {
        return $this->modelo->obtenerEstadisticas();
    }
}