<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\PricesDao;

$pricesDao = new PricesDao();
$autenticationDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/prices', function (Request $request, Response $response, $args) use (
    $pricesDao,
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

    // session_start();
    $id_company = $_SESSION['id_company'];

    $prices = $pricesDao->findAllPricesByCompany($id_company);
    $response->getBody()->write(json_encode($prices));
    return $response->withHeader('Content-Type', 'application/json');
});
