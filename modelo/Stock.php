<?php
class Stock {
    private $pdo;

    // Recibe la conexión PDO desde el controlador
    public function __construct($conexionPDO) {
        $this->pdo = $conexionPDO;
    }

    // Obtiene todos los productos en vitrina con stock mayor a cero
    public function obtenerTodoDisponibles() {
        try {
            $sql = "SELECT nombre, categoria, imagen, stock FROM stock WHERE estado_disponible = 1 AND stock > 0";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Stock::obtenerTodoDisponibles -> " . $e->getMessage());
            return [];
        }
    }

    // Obtiene productos de una categoría específica que tengan stock
    public function obtenerPorCategoriaDisponibles($categoria) {
        try {
            $sql = "SELECT nombre, categoria, imagen, stock FROM stock WHERE estado_disponible = 1 AND stock > 0 AND categoria = :categoria";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['categoria' => $categoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Stock::obtenerPorCategoriaDisponibles -> " . $e->getMessage());
            return [];
        }
    }
}