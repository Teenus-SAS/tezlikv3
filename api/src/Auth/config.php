<?php

use Symfony\Component\Dotenv\Dotenv;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$dotenv = new Dotenv();

// Carga el archivo .env solo si las variables aún no están cargadas
if (!isset($_ENV['DB_HOST'])) {
    $dotenv->loadEnv(dirname(dirname(__DIR__)) . '/environment.env');
}

// Carga las variables de entorno
/* $dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/environment.env'); */