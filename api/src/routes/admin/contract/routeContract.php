<?php

use tezlikv3\dao\ContractDao;

$contractDao = new ContractDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/contracts', function (Request $request, Response $response, $args) use ($contractDao) {
    $resp = $contractDao->findContract();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveContract', function (Request $request, Response $response, $args) use (
    $contractDao
) {
    $dataContract = $request->getParsedBody();

    $contract = $contractDao->findContract();
    if (!$contract)
        $contract = $contractDao->insertContract($dataContract);
    else
        $contract = $contractDao->updateContract($dataContract);

    if ($contract == null)
        $resp = array('success' => true, 'message' => 'Información guardada correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la informacion. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
