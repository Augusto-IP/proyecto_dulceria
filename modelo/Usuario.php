<?php
class Usuario {
    private $pdo;

    public function __construct($conexion) {
        $this->pdo = $conexion;
    }

    public function validar($nombre, $password) {
        $sql = "SELECT * FROM usuario WHERE nombre_usuario = :user AND contrasena = :pass";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user' => $nombre, 'pass' => $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>