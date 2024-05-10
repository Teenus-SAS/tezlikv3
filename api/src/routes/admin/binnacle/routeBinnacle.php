<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\Dao\BinnacleDao;

$binnacleDao = new BinnacleDao();
$autenticationDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/binnacle', function (Request $request, Response $response, $args) use (
    $binnacleDao,
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

    $binnacle = $binnacleDao->findAllBinnacle();
    $response->getBody()->write(json_encode($binnacle));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
