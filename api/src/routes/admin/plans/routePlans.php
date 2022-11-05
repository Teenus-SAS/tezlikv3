<?php

use tezlikv3\dao\PlansDao;

$plansDao = new PlansDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/plans', function (Request $request, Response $response, $args) use ($plansDao) {
    $plans = $plansDao->findAllPlans();

    $response->getBody()->write(json_encode($plans));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/plansAccess', function (Request $request, Response $response, $args) use ($plansDao) {
    $plans = $plansDao->findAllAccessPlans();

    $response->getBody()->write(json_encode($plans));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlansAccess', function (Request $request, Response $response, $args) use ($plansDao) {
    $dataPlan = $request->getParsedBody();

    $plans = $plansDao->updateAccessPlan($dataPlan);

    if ($plans == null)
        $resp = array('success' => true, 'message' => 'Se modificaron los accesos del plan correctamente');
    else if ($plans['info'] == true)
        $resp = array('info' => true, 'message' => $plans['info']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
