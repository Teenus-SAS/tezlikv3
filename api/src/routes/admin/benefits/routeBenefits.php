<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\Dao\BenefitsDao;

$benefitsDao = new BenefitsDao();
$autenticationDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/benefits', function (Request $request, Response $response, $args) use (
    $benefitsDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $benefits = $benefitsDao->findAllBenefits();
    $response->getBody()->write(json_encode($benefits));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateBenefit', function (Request $request, Response $response, $args) use (
    $benefitsDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

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
