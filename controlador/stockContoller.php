<?php
// ============================================================
//  controlador/stockController.php
// ============================================================
 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/Stock.php';
 
class stockController {
 
    private $modelo;
 
    public function __construct() {
        global $pdo;
        $this->modelo = new Stock($pdo);
    }
 
    /** Para la vitrina pública */
    public function obtenerVitrina($categoria = 'todos') {
        if ($categoria !== 'todos') {
            return $this->modelo->obtenerPorCategoriaDisponibles($categoria);
        }
        return $this->modelo->obtenerTodoDisponibles();
    }
 
    /** Para el panel admin */
    public function obtenerTodoAdmin() {
        return $this->modelo->obtenerTodoAdmin();
    }
 
    /** Actualizar producto */
    public function actualizar($id, $nombre, $categoria, $stock, $precio, $estado) {
        return $this->modelo->actualizar($id, $nombre, $categoria, $stock, $precio, $estado);
    }
 
    /** Crear producto */
    public function crear($nombre, $categoria, $imagen, $stock, $precio) {
        return $this->modelo->crear($nombre, $categoria, $imagen, $stock, $precio);
    }
 
    /** Eliminar producto */
    public function eliminar($id) {
        return $this->modelo->eliminar($id);
    }
}