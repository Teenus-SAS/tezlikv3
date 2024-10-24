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

    $binnacle = $binnacleDao->findAllBinnacle();
    $response->getBody()->write(json_encode($binnacle));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
