<?php

use tezlikv3\dao\PriceObjectivesDao;

$priceObjectivesDao = new PriceObjectivesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/priceObjectives', function (Request $request, Response $response, $args) use ($priceObjectivesDao) {
    $id_company = $_SESSION['id_company'];
    $products = $priceObjectivesDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/savePriceObjectives', function (Request $request, Response $response, $args) use ($priceObjectivesDao) {
    $id_company = $_SESSION['id_company'];
    $dataPrice = $request->getParsedBody();
    $products = $dataPrice['products'];
    $resolution = null;

    for ($i = 0; $i < sizeof($products); $i++) {
        if (isset($resolution['info'])) break;
        $findPriceObjective = $priceObjectivesDao->findPriceObjectiveByProduct($products[$i]['id_product']);

        if (!$findPriceObjective)
            $resolution = $priceObjectivesDao->insertPriceObjectiveByCompany($products[$i], $id_company);
        else
            $resolution = $priceObjectivesDao->updatePriceObjective($products[$i]);
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Objetivos de precios guardados correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
