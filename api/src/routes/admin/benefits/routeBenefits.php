<?php

use tezlikv3\Dao\BenefitsDao;

$benefitsDao = new BenefitsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/benefits', function (RouteCollectorProxy $group) use ($benefitsDao) {

    $group->get('', function (Request $request, Response $response, $args) use ($benefitsDao) {
        $benefits = $benefitsDao->findAllBenefits();
        $response->getBody()->write(json_encode($benefits));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/updateBenefit', function (Request $request, Response $response, $args) use ($benefitsDao) {
        $dataBenefit = $request->getParsedBody();

        $benefits = $benefitsDao->updateBenefit($dataBenefit);

        if ($benefits == null)
            $resp = array('success' => true, 'message' => 'Prestación modificada correctamente');
        else if (isset($benefits['info']))
            $resp = array('info' => true, 'message' => $benefits['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar la información');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
