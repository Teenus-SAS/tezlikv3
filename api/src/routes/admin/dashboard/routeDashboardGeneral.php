<?php

use tezlikv3\dao\DashboardGeneralsDao;

$dashboardGeneralsDao = new DashboardGeneralsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardCountsGeneral', function (Request $request, Response $response, $args) use ($dashboardGeneralsDao) {

    // Obtener Cantidad de Productos
    $products = $dashboardGeneralsDao->findAllProducts();

    // Obtener Cantidad de Empresas
    $companies = $dashboardGeneralsDao->findAllCompanies();

    // Obtener Cantidad de Usuarios
    $users = $dashboardGeneralsDao->findAllUsers();

    // Obtener Cantidad de Usuarios Activos
    $usersSession = $dashboardGeneralsDao->findAllActiveUsersSession();

    // Obtener Cantidad Empresas y Usuarios activos
    $sCompany = $dashboardGeneralsDao->findAllComaniesAndUsers();
    // Obtener Cantidad Mese activo
    $month = $dashboardGeneralsDao->findAllCountByMonth();

    $generalDashboardCounts['allProducts'] = $products;
    $generalDashboardCounts['allCompanies'] = $companies;
    $generalDashboardCounts['allUsers'] = $users;
    $generalDashboardCounts['allUsersSession'] = $usersSession;
    $generalDashboardCounts['sCompany'] = $sCompany;
    $generalDashboardCounts['month'] = $month;

    $response->getBody()->write(json_encode($generalDashboardCounts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
