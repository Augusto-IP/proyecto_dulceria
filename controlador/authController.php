<?php
// controlador/authController.php
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../modelo/Usuario.php';

class authController {
    public function login($nombre, $password) {
        global $pdo; // Esto es vital para que use la conexión de database.php
        
        if (!$pdo) {
            return false; 
        }

        $usuarioModel = new Usuario($pdo);
        $datos = $usuarioModel->validar($nombre, $password);
        
        if ($datos) {
            $_SESSION['id_usuario'] = $datos['id_usuario'];
            $_SESSION['usuario'] = $datos['nombre_usuario'];
            return true;
        }
        return false;
    }
}