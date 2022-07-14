<?php

use tezlikv3\dao\ProgrammingDao;

$programmingDao = new ProgrammingDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/programming', function (Request $request, Response $response, $args) use ($programmingDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $programmingDao->findAllProgramming($id_company);
    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
