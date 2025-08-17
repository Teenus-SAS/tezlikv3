<?php

use tezlikv3\dao\{GeneralProductMaterialsDao};

$generalProductMaterialsDao = new GeneralProductMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/productsMaterialsBasic', function (Request $request, Response $response, $args) use ($generalProductMaterialsDao) {
    // session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $generalProductMaterialsDao->findDataBasicProductsMaterials($id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
