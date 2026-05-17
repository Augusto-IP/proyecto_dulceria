<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/PedidosPersonalizados.php';

class pedidosPersonalizadosController {

    private $modelo;

    public function __construct() {
        global $pdo;
        $this->modelo = new PedidosPersonalizados($pdo);
    }

    public function obtenerMisPedidos($id_usuario) {
        return $this->modelo->obtenerPorUsuario($id_usuario);
    }

    public function obtenerTodos() {
        return $this->modelo->obtenerTodos();
    }

    public function obtenerPorId($id_pedido_per) {
        return $this->modelo->obtenerPorId($id_pedido_per);
    }

    public function registrar($id_usuario, $nombre_pedido, $descripcion, $tamaño, $presupuesto, $fecha_entrega, $cantidad_personas = 1, $foto = null, $comentarios = '') {
        return $this->modelo->crear($id_usuario, $nombre_pedido, $descripcion, $tamaño, $presupuesto, $fecha_entrega, $cantidad_personas, $foto, $comentarios);
    }

    public function actualizar($id_pedido_per, $nombre_pedido, $descripcion, $tamaño, $presupuesto, $fecha_entrega, $cantidad_personas, $comentarios = '') {
        return $this->modelo->actualizar($id_pedido_per, $nombre_pedido, $descripcion, $tamaño, $presupuesto, $fecha_entrega, $cantidad_personas, $comentarios);
    }

    public function cambiarEstado($id_pedido_per, $estado) {
        return $this->modelo->cambiarEstado($id_pedido_per, $estado);
    }

    public function eliminar($id_pedido_per) {
        return $this->modelo->eliminar($id_pedido_per);
    }

    public function obtenerEstadisticas() {
        return $this->modelo->obtenerEstadisticas();
    }
}
?>
