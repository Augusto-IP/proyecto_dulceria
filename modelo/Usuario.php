<?php
// ============================================================
//  modelo/Usuario.php
// ============================================================
 
class Usuario {
    private $pdo;
 
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
    /**
     * Valida usuario y contraseña. Retorna datos o false.
     */
    public function validar($usuario, $password) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id_trabajador, nombre_completo, usuario, password, rol
                 FROM trabajadores
                 WHERE usuario = :usuario AND activo = 1
                 LIMIT 1"
            );
            $stmt->execute(['usuario' => $usuario]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
 
            if ($datos && password_verify($password, $datos['password'])) {
                return $datos;
            }
            return false;
        } catch (PDOException $e) {
            error_log('Error en Usuario::validar -> ' . $e->getMessage());
            return false;
        }
    }
 
    /**
     * Obtiene todos los usuarios (para panel admin).
     */
    public function obtenerTodos() {
        try {
            $stmt = $this->pdo->query(
                "SELECT id_trabajador, nombre_completo, usuario, rol, activo, created_at
                 FROM trabajadores ORDER BY created_at DESC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Usuario::obtenerTodos -> ' . $e->getMessage());
            return [];
        }
    }
 
    /**
     * Crea un nuevo usuario.
     */
    public function crear($nombre, $usuario, $password, $rol = 'empleado') {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare(
                "INSERT INTO trabajadores (nombre_completo, usuario, password, rol)
                 VALUES (:nombre, :usuario, :password, :rol)"
            );
            return $stmt->execute([
                'nombre'   => $nombre,
                'usuario'  => $usuario,
                'password' => $hash,
                'rol'      => $rol,
            ]);
        } catch (PDOException $e) {
            error_log('Error en Usuario::crear -> ' . $e->getMessage());
            return false;
        }
    }
}