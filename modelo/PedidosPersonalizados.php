<?php
// ============================================================
//  modelo/PedidosPersonalizados.php
// ============================================================

class PedidosPersonalizados {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene pedidos personalizados del usuario
     */
    public function obtenerPorUsuario($id_usuario) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM pedidos_personalizados
                WHERE id_usuario = :id_usuario
                ORDER BY fecha_pedido DESC
            ");
            $stmt->execute(['id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::obtenerPorUsuario -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los pedidos personalizados (admin)
     */
    public function obtenerTodos() {
        try {
            $stmt = $this->pdo->query("
                SELECT pp.*, t.nombre_completo
                FROM pedidos_personalizados pp
                LEFT JOIN trabajadores t ON pp.id_usuario = t.id_trabajador
                ORDER BY pp.fecha_pedido DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::obtenerTodos -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un pedido personalizado por ID
     */
    public function obtenerPorId($id_pedido_per) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT pp.*, t.nombre_completo
                FROM pedidos_personalizados pp
                LEFT JOIN trabajadores t ON pp.id_usuario = t.id_trabajador
                WHERE pp.id_pedido_per = :id
                LIMIT 1
            ");
            $stmt->execute(['id' => $id_pedido_per]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::obtenerPorId -> ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Crea un nuevo pedido personalizado
     */
    public function crear($id_usuario, $nombre_pedido, $descripcion, $tamaño, $presupuesto, $fecha_entrega, $cantidad_personas = 1, $foto = null, $comentarios = '') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO pedidos_personalizados (
                    id_usuario, nombre_pedido, descripcion, tamaño, presupuesto, 
                    fecha_pedido, fecha_entrega, cantidad_personas, foto, comentarios, estado
                )
                VALUES (
                    :id_usuario, :nombre_pedido, :descripcion, :tamaño, :presupuesto,
                    :fecha_pedido, :fecha_entrega, :cantidad_personas, :foto, :comentarios, 'pendiente_revision'
                )
            ");
            return $stmt->execute([
                'id_usuario'         => $id_usuario,
                'nombre_pedido'      => $nombre_pedido,
                'descripcion'        => $descripcion,
                'tamaño'             => $tamaño,
                'presupuesto'        => $presupuesto,
                'fecha_pedido'       => date('Y-m-d'),
                'fecha_entrega'      => $fecha_entrega,
                'cantidad_personas'  => $cantidad_personas,
                'foto'               => $foto,
                'comentarios'        => $comentarios,
            ]);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::crear -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un pedido personalizado
     */
    public function actualizar($id_pedido_per, $nombre_pedido, $descripcion, $tamaño, $presupuesto, $fecha_entrega, $cantidad_personas, $comentarios = '') {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE pedidos_personalizados SET
                    nombre_pedido = :nombre_pedido,
                    descripcion = :descripcion,
                    tamaño = :tamaño,
                    presupuesto = :presupuesto,
                    fecha_entrega = :fecha_entrega,
                    cantidad_personas = :cantidad_personas,
                    comentarios = :comentarios
                WHERE id_pedido_per = :id
            ");
            return $stmt->execute([
                'nombre_pedido'      => $nombre_pedido,
                'descripcion'        => $descripcion,
                'tamaño'             => $tamaño,
                'presupuesto'        => $presupuesto,
                'fecha_entrega'      => $fecha_entrega,
                'cantidad_personas'  => $cantidad_personas,
                'comentarios'        => $comentarios,
                'id'                 => $id_pedido_per,
            ]);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::actualizar -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado de un pedido personalizado
     */
    public function cambiarEstado($id_pedido_per, $estado) {
        try {
            $stmt = $this->pdo->prepare("UPDATE pedidos_personalizados SET estado = :estado WHERE id_pedido_per = :id");
            return $stmt->execute(['estado' => $estado, 'id' => $id_pedido_per]);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::cambiarEstado -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un pedido personalizado
     */
    public function eliminar($id_pedido_per) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pedidos_personalizados WHERE id_pedido_per = :id");
            return $stmt->execute(['id' => $id_pedido_per]);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::eliminar -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene estadísticas
     */
    public function obtenerEstadisticas() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente_revision' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'listo' THEN 1 ELSE 0 END) as listos,
                    SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados
                FROM pedidos_personalizados
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en PedidosPersonalizados::obtenerEstadisticas -> ' . $e->getMessage());
            return null;
        }
    }
}
?>
