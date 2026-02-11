<?php

/**
 * Excepción de aplicación para indicar indisponibilidad de la base de datos.
 *
 * Se lanza desde repositorios/servicios cuando no es posible completar una
 * operación por fallo de conexión o consulta.
 *
 * @package App\Exceptions
 */

namespace App\Exceptions;

use Exception;

/**
 * Excepción lanzada cuando la base de datos no está disponible.
 *
 * @package App\Exceptions
 */
class DatabaseUnavailableException extends Exception
{
}