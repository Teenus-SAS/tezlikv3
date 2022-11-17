<?php

use tezlikv3\dao\QuotesDao;

$quotesDao = new QuotesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consultar Todos */

$app->get('/quotes', function (Request $request, Response $response, $args) use ($quotesDao) {
    $quotes = $quotesDao->findAllQuotes();
    $response->getBody()->write(json_encode($quotes, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateQuote', function (Request $request, Response $response, $args) use ($quotesDao) {
    $dataQuote = $request->getParsedBody();

    if (
        empty($dataQuote['idProduct']) || empty($dataQuote['quantity']) || empty($dataQuote['discount']) ||
        empty($dataQuote['offerValidity']) || empty($dataQuote['warranty']) || empty($dataQuote['idPaymentMethod'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese los campos');
    else {
        $quotes = $quotesDao->updateQuote($dataQuote);

        if ($quotes == null)
            $resp = array('success' => true, 'message' => 'Cotizacion modificada correctamente');
        else if (isset($quotes['info']))
            $resp = array('info' => true, 'message' => $quotes['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteQuote/{id_quote}', function (Request $request, Response $response, $args) use ($quotesDao) {
    $quotes = $quotesDao->deleteQuote($args['id_quote']);

    if ($quotes == null)
        $resp = array('success' => true, 'message' => 'Cotización eliminada correctamente');
    else if (isset($quotes['info']))
        $resp = array('info' => true, 'message' => $quotes['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
