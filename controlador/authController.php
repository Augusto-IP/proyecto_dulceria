<?php
// controlador/authController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/Usuario.php';

class authController {
    public function login($nombre, $password) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        global $pdo;
        if (!$pdo) {
            return false;
        }

        $usuarioModel = new Usuario($pdo);
        $datos = $usuarioModel->validar($nombre, $password);

        if ($datos) {
            $_SESSION['id_usuario'] = $datos['id_trabajador'] ?? null;
            $_SESSION['usuario'] = $datos['usuario'] ?? $nombre;
            $_SESSION['nombre'] = $datos['nombre_completo'] ?? '';
            $_SESSION['rol'] = $datos['rol'] ?? null;
            return true;
        }
        return false;
    }
}