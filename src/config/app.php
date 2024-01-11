<?php 
// Definimos el directorio padre del sistema
define("DIR_ROOT", dirname(dirname(__DIR__)));
// Activamos el autoloader de Composer
require DIR_ROOT.'/vendor/autoload.php';
// Cargamos las funciones de utilidad
require_once DIR_ROOT.'/util/funciones.php';