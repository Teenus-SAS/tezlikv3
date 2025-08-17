<?php

use tezlikv3\dao\{
    GeneralProductsDao,
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;


$app->get('/selectProducts', function (Request $request, Response $response, $args) {
    $generalProductsDao = new GeneralProductsDao();

    $id_company = $_SESSION['id_company'];
    try {
        $products = $generalProductsDao->findDataBasicProductsByCompany($id_company);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener los productos'], 500);
    }
})->add(new SessionMiddleware());
