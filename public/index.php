<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Marca el instante exacto en el que arranca la ejecución (se usa para medir tiempos/rendimiento).
define('LARAVEL_START', microtime(true));

// Comprueba si la app está en modo mantenimiento.
// Si existe el archivo maintenance.php, se carga y normalmente devuelve una respuesta de mantenimiento.
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Carga el autoloader de Composer para poder usar clases instaladas con Composer
require __DIR__.'/../vendor/autoload.php';

// Inicia (bootstrap) la aplicación Laravel cargando el contenedor y la configuración base.
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Captura la petición HTTP actual (Request::capture()) y se la pasa a Laravel para que la procese
// (routing, middleware, controlador, respuesta, etc.).
$app->handleRequest(Request::capture());
