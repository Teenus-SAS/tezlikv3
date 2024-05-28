<?php

use tezlikv3\Dao\RisksDao;
use tezlikv3\dao\WebTokenDao;

$risksDao = new RisksDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/risks', function (Request $request, Response $response, $args) use (
    $risksDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $risks = $risksDao->findAllRisks();
    $response->getBody()->write(json_encode($risks));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateRisk', function (Request $request, Response $response, $args) use (
    $risksDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $dataRisk = $request->getParsedBody();

    $risks = $risksDao->updateRisk($dataRisk);

    if ($risks == null)
        $resp = array('success' => true, 'message' => 'Nivel de riesgo modificada correctamente');
    else if (isset($risks['info']))
        $resp = array('info' => true, 'message' => $risks['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar la información');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
