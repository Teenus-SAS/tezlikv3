<?php

use tezlikv3\dao\{GeneralMachinesDao};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/selectMachines', function (Request $request, Response $response, $args) {
    // session_start();
    $id_company = $_SESSION['id_company'];

    $generalMachinesDao = new GeneralMachinesDao();

    $machines = $generalMachinesDao->findDataBasicMachinesByCompany($id_company);
    $response->getBody()->write(json_encode($machines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
