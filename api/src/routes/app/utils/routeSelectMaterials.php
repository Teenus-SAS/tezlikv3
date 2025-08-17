<?php

use tezlikv3\dao\{GeneralMaterialsDao};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/selectMaterials', function (Request $request, Response $response, $args) {
    $generalMaterialsDao = new GeneralMaterialsDao();

    // session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $generalMaterialsDao->findDataBasicMaterialsByCompany($id_company);
    $response->getBody()->write(json_encode($materials));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
