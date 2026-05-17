ck · PHP
Copiar

<?php
// ============================================================
//  modelo/Stock.php
// ============================================================
 
class Stock {
    private $pdo;
 
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
    public function obtenerTodoDisponibles() {
        try {
            $stmt = $this->pdo->query(
                "SELECT id_stock, nombre, categoria, imagen, stock, precio
                 FROM stock WHERE estado_disponible = 1 AND stock > 0
                 ORDER BY categoria, nombre"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Stock::obtenerTodoDisponibles -> ' . $e->getMessage());
            return [];
        }
    }
 
    public function obtenerPorCategoriaDisponibles($categoria) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id_stock, nombre, categoria, imagen, stock, precio
                 FROM stock WHERE estado_disponible = 1 AND stock > 0 AND categoria = :categoria
                 ORDER BY nombre"
            );
            $stmt->execute(['categoria' => $categoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Stock::obtenerPorCategoriaDisponibles -> ' . $e->getMessage());
            return [];
        }
    }
 
    public function obtenerTodoAdmin() {
        try {
            $stmt = $this->pdo->query(
                "SELECT * FROM stock ORDER BY categoria, nombre"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Stock::obtenerTodoAdmin -> ' . $e->getMessage());
            return [];
        }
    }
 
    public function actualizar($id, $nombre, $categoria, $stock, $precio, $estado) {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE stock SET nombre=:nombre, categoria=:categoria,
                 stock=:stock, precio=:precio, estado_disponible=:estado
                 WHERE id_stock=:id"
            );
            return $stmt->execute([
                'nombre'    => $nombre,
                'categoria' => $categoria,
                'stock'     => $stock,
                'precio'    => $precio,
                'estado'    => $estado,
                'id'        => $id,
            ]);
        } catch (PDOException $e) {
            error_log('Error en Stock::actualizar -> ' . $e->getMessage());
            return false;
        }
    }
 
    public function crear($nombre, $categoria, $imagen, $stock, $precio) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO stock (nombre, categoria, imagen, stock, precio)
                 VALUES (:nombre, :categoria, :imagen, :stock, :precio)"
            );
            return $stmt->execute([
                'nombre'    => $nombre,
                'categoria' => $categoria,
                'imagen'    => $imagen,
                'stock'     => $stock,
                'precio'    => $precio,
            ]);
        } catch (PDOException $e) {
            error_log('Error en Stock::crear -> ' . $e->getMessage());
            return false;
        }
    }
 
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM stock WHERE id_stock = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log('Error en Stock::eliminar -> ' . $e->getMessage());
            return false;
        }
    }
}