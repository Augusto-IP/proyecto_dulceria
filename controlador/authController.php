<?php
// ============================================================
//  controlador/authController.php
// ============================================================
 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/Usuario.php';
 
class authController {
 
    public function login($nombre, $password) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
 
        global $pdo;
        if (!$pdo) return false;
 
        $usuarioModel = new Usuario($pdo);
        $datos = $usuarioModel->validar($nombre, $password);
 
        if ($datos) {
            $_SESSION['id_usuario']     = $datos['id_trabajador'];
            $_SESSION['usuario']        = $datos['usuario'];
            $_SESSION['nombre']         = $datos['nombre_completo'];
            $_SESSION['rol']            = $datos['rol'];
            return true;
        }
        return false;
    }
 
    public function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }
 
    public static function requireLogin() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: ../vista/login.php');
            exit();
        }
    }
 
    public static function requireAdmin() {
        self::requireLogin();
        if ($_SESSION['rol'] !== 'admin') {
            header('Location: ../vista/home.php');
            exit();
        }
    }
}