<?php

use tezlikv3\dao\ConsolidatedDao;

$consolidatedDao = new ConsolidatedDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/consolidated', function (Request $request, Response $response, $args) use ($consolidatedDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $consolidated = $consolidatedDao->findConsolidated($id_company);
    $response->getBody()->write(json_encode($consolidated, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* $app->post('/addConsolidated', function (Request $request, Response $response, $args) use ($consolidatedDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataConsolidated = $request->getParsedBody();
}); */
