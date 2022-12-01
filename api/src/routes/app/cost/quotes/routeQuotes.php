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

/* Consultar detalle de cotizacion */
$app->get('/quote/{id_quote}', function (Request $request, Response $response, $args) use ($quotesDao) {
    $quote = $quotesDao->findQuote($args['id_quote']);

    $quotesProducts = $quotesDao->findAllQuotesProductsByIdQuote($args['id_quote']);

    $data['quote'] = $quote;
    $data['quotesProducts'] = $quotesProducts;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/quotesProducts/{id_quote}', function (Request $request, Response $response, $args) use ($quotesDao) {
    $quotesProducts = $quotesDao->findAllQuotesProductsByIdQuote($args['id_quote']);
    $response->getBody()->write(json_encode($quotesProducts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addQuote', function (Request $request, Response $response, $arsg) use ($quotesDao) {
    $dataQuote = $request->getParsedBody();

    $quote = $quotesDao->insertQuote($dataQuote);

    if ($quote == null) {
        /* Obtener id cotizacion */
        $quote = $quotesDao->findLastQuote();
        /* Inserta todos los productos de la cotizacion */
        $quotesProducts = $quotesDao->insertQuotesProducts($dataQuote, $quote['id_quote']);

        if ($quotesProducts == null)
            $resp = array('success' => true, 'message' => 'Cotización insertada correctamente');
        else if (isset($quotesProducts['info']))
            $resp = array('info' => true, 'message' => $quotesProducts['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateQuote', function (Request $request, Response $response, $args) use ($quotesDao) {
    $dataQuote = $request->getParsedBody();

    $quote = $quotesDao->updateQuote($dataQuote);

    if ($quote == null)
        /* Elimina todos los productos de la cotizacion */
        $quotesProducts = $quotesDao->deleteQuotesProducts($dataQuote['idQuote']);

    if ($quotesProducts == null)
        /* Inserta todos los productos de la cotizacion */
        $quotesProducts = $quotesDao->insertQuotesProducts($dataQuote, $dataQuote['idQuote']);

    if ($quotesProducts == null)
        $resp = array('success' => true, 'message' => 'Cotizacion modificada correctamente');
    else if (isset($quotesProducts['info']))
        $resp = array('info' => true, 'message' => $quotesProducts['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteQuote/{id_quote}', function (Request $request, Response $response, $args) use ($quotesDao) {
    /* Elimina todos los productos de la cotizacion */
    $quotesProducts = $quotesDao->deleteQuotesProducts($args['id_quote']);

    if ($quotesProducts == null)
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
