<?php
// Inicia sesión para poder utilizar sus variables
session_start();

// Para saber si estamos en servidor local
define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
$web_url = IS_LOCAL ? 'http://localhost/cotizador/' : 'LA URL DE SU SERVIDOR EN PRODUCCIÓN';
define('URL', $web_url);

// Rutas para carpetas
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', getcwd() . DS);
define('APP', ROOT . 'app' . DS);
define('ASSETS', ROOT . 'assets' . DS);
define('TEMPLATES', ROOT . 'templates' . DS);
define('INCLUDES', TEMPLATES . 'includes' . DS);
define('MODULES', TEMPLATES . 'modules' . DS);
define('VIEWS', TEMPLATES . 'views' . DS);
define('UPLOADS', 'assets/uploads' . DS);

// Para archivos que se vayan a incluir en header o footer(css o js)
define('CSS', URL . 'assets/css/');
define('IMG', URL . 'assets/img/');
define('JS', URL . 'assets/js/');

// Personalización
define('APP_NAME', 'Cotizador App');
define('TAXES_RATE', 16);
define('SHIPPING', 25.250);

// Dependencias PDF
require_once ROOT . 'vendor/autoload.php';

// Cargar Todas las funciones
require_once APP . 'functions.php';
