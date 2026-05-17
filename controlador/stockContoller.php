<?php
// Controlador/stockController.php

// 1. Importamos la conexión de tu base de datos y el modelo
// (Asegúrate de que la ruta a tu 'database.php' sea la correcta)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/Stock.php';

$productos_vitrina = [];
$categoria_activa = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';

if (isset($pdo)) {
    // Inicializamos el modelo pasándole la conexión PDO
    $modeloStock = new Stock($pdo);

    // Decidimos qué método del modelo ejecutar según el filtro de la pantalla
    if ($categoria_activa !== 'todos') {
        $productos_vitrina = $modeloStock->obtenerPorCategoriaDisponibles($categoria_activa);
    } else {
        $productos_vitrina = $modeloStock->obtenerTodoDisponibles();
    }
}