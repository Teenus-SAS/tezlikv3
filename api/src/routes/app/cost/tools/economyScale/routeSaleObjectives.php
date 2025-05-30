<?php

use tezlikv3\dao\SaleObjectivesDao;

$saleObjectivesDao = new SaleObjectivesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/saleObjectives', function (Request $request, Response $response, $args) use ($saleObjectivesDao) {
    $id_company = $_SESSION['id_company'];
    $products = $saleObjectivesDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/saveSaleObjectives', function (Request $request, Response $response, $args) use ($saleObjectivesDao) {
    $id_company = $_SESSION['id_company'];
    $dataSale = $request->getParsedBody();
    $products = $dataSale['products'];
    $resolution = null;

    for ($i = 0; $i < sizeof($products); $i++) {
        if (isset($resolution['info'])) break;
        $findSaleObjective = $saleObjectivesDao->findSaleObjectiveByProduct($products[$i]['id_product']);

        if (!$findSaleObjective)
            $resolution = $saleObjectivesDao->insertSaleObjectiveByCompany($products[$i], $id_company);
        else
            $resolution = $saleObjectivesDao->updateSaleObjective($products[$i]);
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Objetivos de ventas guardados correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
