<?php

use tezlikv3\dao\GeneralUsersDao;
use tezlikv3\dao\WebTokenDao;

$generalUsersDao = new GeneralUsersDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/quantityUsers', function (Request $request, Response $response, $args) use (
    $generalUsersDao,
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

    // session_start();
    //$dataQuantityUser = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $quantityUsersAllows = $generalUsersDao->quantityUsersAllows($id_company);
    $quantityUsersCreated = $generalUsersDao->quantityUsersCreated($id_company);
    $response->getBody()->write(json_encode($quantityUsersAllows, $quantityUsersCreated, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
