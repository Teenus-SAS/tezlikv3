<?php

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

// ✅ Carga entorno 
require_once dirname(__DIR__) . '/api/src/Auth/config.php';

//require __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/AutoloaderSourceCode.php';

$app = AppFactory::create();
$app->setBasePath('/api');

$app->addBodyParsingMiddleware();

// ✅ Carga Dispatcher
require_once __DIR__ . '/src/routes/RouteDispatcher.php';

// ✅ Obtiene ruta actual
$request = ServerRequestFactory::createFromGlobals();
$path = $request->getUri()->getPath();
$method = $request->getMethod();

// Remueve el prefijo /api si existe
$normalizedPath = preg_replace('#^/api#', '', $path);

// ✅ Despacha solo las rutas necesarias
RouteDispatcher::dispatch($app, $normalizedPath);

// ✅ Ejecuta la app
$app->run();
