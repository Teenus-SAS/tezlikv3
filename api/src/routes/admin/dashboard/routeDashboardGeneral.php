<?php

use tezlikv3\dao\DashboardGeneralsDao;

$dashboardGeneralsDao = new DashboardGeneralsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/dashboardCountsGeneral', function (Request $request, Response $response, $args) use ($dashboardGeneralsDao) {
    // Obtener Cantidad de Productos
    $products = $dashboardGeneralsDao->findAllProducts();

    // Obtener Cantidad de Empresas Activas
    $companies = $dashboardGeneralsDao->findAllCompanies();

    // Obtener Cantidad de Usuarios Activos
    $users = $dashboardGeneralsDao->findAllUsersActive();

    // Obtener Cantidad de Usuarios En sesion
    $usersSession = $dashboardGeneralsDao->findAllActiveUsersSession();

    // Obtener todos los ingresos
    $allRecords = $dashboardGeneralsDao->findAllRecordsByYear();

    $generalDashboardCounts['allProducts'] = $products;
    $generalDashboardCounts['allCompanies'] = $companies;
    $generalDashboardCounts['allUsers'] = $users;
    $generalDashboardCounts['allUsersSession'] = $usersSession;
    $generalDashboardCounts['allRecords'] = $allRecords;

    $response->getBody()->write(json_encode($generalDashboardCounts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
