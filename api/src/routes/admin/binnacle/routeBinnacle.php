<?php

use tezlikv3\Dao\BinnacleDao;
use tezlikv3\dao\WebTokenDao;

$binnacleDao = new BinnacleDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/binnacle', function (Request $request, Response $response, $args) use (
    $binnacleDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $binnacle = $binnacleDao->findAllBinnacle();
    $response->getBody()->write(json_encode($binnacle));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
