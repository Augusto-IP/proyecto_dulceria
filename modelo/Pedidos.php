<?php
// ============================================================
//  modelo/Pedidos.php
// ============================================================

class Pedidos {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los pedidos del usuario
     */
    public function obtenerPorUsuario($id_usuario) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, s.nombre_servicio, s.precio_base
                FROM pedidos p
                LEFT JOIN servicios s ON p.id_servicio = s.id_servicio
                WHERE p.id_usuario = :id_usuario
                ORDER BY p.fecha_pedido DESC
            ");
            $stmt->execute(['id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::obtenerPorUsuario -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los pedidos (admin)
     */
    public function obtenerTodos() {
        try {
            $stmt = $this->pdo->query("
                SELECT p.*, s.nombre_servicio, t.nombre_completo
                FROM pedidos p
                LEFT JOIN servicios s ON p.id_servicio = s.id_servicio
                LEFT JOIN trabajadores t ON p.id_usuario = t.id_trabajador
                ORDER BY p.fecha_pedido DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::obtenerTodos -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un pedido por ID
     */
    public function obtenerPorId($id_pedido) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, s.nombre_servicio, s.precio_base, t.nombre_completo
                FROM pedidos p
                LEFT JOIN servicios s ON p.id_servicio = s.id_servicio
                LEFT JOIN trabajadores t ON p.id_usuario = t.id_trabajador
                WHERE p.id_pedido = :id
                LIMIT 1
            ");
            $stmt->execute(['id' => $id_pedido]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::obtenerPorId -> ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Crea un nuevo pedido
     */
    public function crear($id_usuario, $id_servicio, $cantidad, $fecha_entrega, $notas = '') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO pedidos (id_usuario, id_servicio, cantidad, fecha_pedido, fecha_entrega, notas, estado)
                VALUES (:id_usuario, :id_servicio, :cantidad, :fecha_pedido, :fecha_entrega, :notas, 'pendiente')
            ");
            return $stmt->execute([
                'id_usuario'     => $id_usuario,
                'id_servicio'    => $id_servicio,
                'cantidad'       => $cantidad,
                'fecha_pedido'   => date('Y-m-d'),
                'fecha_entrega'  => $fecha_entrega,
                'notas'          => $notas,
            ]);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::crear -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un pedido
     */
    public function actualizar($id_pedido, $cantidad, $fecha_entrega, $notas = '', $estado = null) {
        try {
            $updates = "cantidad = :cantidad, fecha_entrega = :fecha_entrega, notas = :notas";
            if ($estado) {
                $updates .= ", estado = :estado";
            }
            
            $stmt = $this->pdo->prepare("UPDATE pedidos SET $updates WHERE id_pedido = :id");
            
            $params = [
                'cantidad'       => $cantidad,
                'fecha_entrega'  => $fecha_entrega,
                'notas'          => $notas,
                'id'             => $id_pedido,
            ];
            if ($estado) {
                $params['estado'] = $estado;
            }
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::actualizar -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado de un pedido
     */
    public function cambiarEstado($id_pedido, $estado) {
        try {
            $stmt = $this->pdo->prepare("UPDATE pedidos SET estado = :estado WHERE id_pedido = :id");
            return $stmt->execute(['estado' => $estado, 'id' => $id_pedido]);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::cambiarEstado -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un pedido
     */
    public function eliminar($id_pedido) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id_pedido = :id");
            return $stmt->execute(['id' => $id_pedido]);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::eliminar -> ' . $e->getMessage());
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
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'listo' THEN 1 ELSE 0 END) as listos,
                    SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados
                FROM pedidos
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Pedidos::obtenerEstadisticas -> ' . $e->getMessage());
            return null;
        }
    }
}
?>
