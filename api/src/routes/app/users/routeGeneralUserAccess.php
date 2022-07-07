<?php

use tezlikv3\dao\GeneralUserAccessDao;

$userAccessDao = new GeneralUserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/generalUserAccess/{id_user}', function (Request $request, Response $response, $args) use ($userAccessDao) {
    $usersAcces = $userAccessDao->findUserAccessByUser($args['id_user']);
    $response->getBody()->write(json_encode($usersAcces, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
