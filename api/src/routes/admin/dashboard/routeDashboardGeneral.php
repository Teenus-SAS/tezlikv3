<?php

use tezlikv3\dao\DashboardGeneralsDao;
use tezlikv3\dao\WebTokenDao;

$dashboardGeneralsDao = new DashboardGeneralsDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardCountsGeneral', function (Request $request, Response $response, $args) use (
    $dashboardGeneralsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

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
    $year = $dashboardGeneralsDao->findAllCountByYear();

    // Obtener Cantidad total por empresa
    $count = $dashboardGeneralsDao->findAllCountByCompany();

    $generalDashboardCounts['allProducts'] = $products;
    $generalDashboardCounts['allCompanies'] = $companies;
    $generalDashboardCounts['allUsers'] = $users;
    $generalDashboardCounts['allUsersSession'] = $usersSession;
    $generalDashboardCounts['sCompany'] = $sCompany;
    $generalDashboardCounts['month'] = $month;
    $generalDashboardCounts['year'] = $year;
    $generalDashboardCounts['count'] = $count;

    $response->getBody()->write(json_encode($generalDashboardCounts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
