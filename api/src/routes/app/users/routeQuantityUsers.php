<?php

use tezlikv3\dao\GeneralUsersDao;

$generalUsersDao = new GeneralUsersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/quantityUsers', function (Request $request, Response $response, $args) use ($generalUsersDao) {
    $id_company = $_SESSION['id_company'];

    $quantityUsersAllows = $generalUsersDao->quantityUsersAllows($id_company);
    $quantityUsersCreated = $generalUsersDao->quantityUsersCreated($id_company);
    $response->getBody()->write(json_encode($quantityUsersAllows, $quantityUsersCreated, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
