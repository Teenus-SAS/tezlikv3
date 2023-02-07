<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\GeneralQuotesDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\QuoteProductsDao;
use tezlikv3\dao\QuotesDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\SendMakeEmailDao;

$quotesDao = new QuotesDao();
$quoteProductsDao = new QuoteProductsDao();
$lastDataDao = new LastDataDao();
$generalQuotesDao = new GeneralQuotesDao();
$convertDataDao = new ConvertDataDao();
$sendEmailDao = new SendEmailDao();
$sendMakeEmailDao = new SendMakeEmailDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consultar Todos */

$app->get('/quotes', function (Request $request, Response $response, $args) use ($quotesDao) {
    $quotes = $quotesDao->findAllQuotes();
    $response->getBody()->write(json_encode($quotes, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Clonar cotización */
$app->get('/copyQuote/{id_quote}', function (Request $request, Response $response, $args) use (
    $quotesDao,
    $quoteProductsDao,
    $lastDataDao,
    $convertDataDao
) {
    $quote = $quotesDao->findQuote($args['id_quote']);

    $dataQuote['company'] = $quote['id_company'];
    $dataQuote['contact'] = $quote['id_contact'];
    $dataQuote['idPaymentMethod'] = $quote['id_payment_method'];
    $dataQuote['offerValidity'] = $quote['offer_validity'];
    $dataQuote['warranty'] = $quote['warranty'];
    $dataQuote['deliveryDate'] = $quote['delivery_date'];
    $dataQuote['observation'] = $quote['observation'];
    // $dataQuote['products'] = $products;

    $respquote = $quotesDao->insertQuote($dataQuote);

    $products = $quoteProductsDao->findAllQuotesProductsByIdQuote($args['id_quote']);

    $lastQuote = $lastDataDao->findLastQuote();

    for ($i = 0; $i < sizeof($products); $i++) {
        $product = $convertDataDao->strReplaceQuotes($products[$i]);
        $resp = $quoteProductsDao->insertQuotesProducts($product, $lastQuote['id_quote']);
    }

    if ($respquote == null && $resp == null)
        $resp = array('success' => true, 'message' => 'Cotización copiada correctamente');
    else
        $resp = array('error' => true, 'message' => 'La Cotización no puede ser copiada, Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar detalle de cotizacion */
$app->get('/quote/{id_quote}', function (Request $request, Response $response, $args) use ($quotesDao, $quoteProductsDao) {
    $quote = $quotesDao->findQuote($args['id_quote']);

    $quotesProducts = $quoteProductsDao->findAllQuotesProductsByIdQuote($args['id_quote']);

    $data['quote'] = $quote;
    $data['quotesProducts'] = $quotesProducts;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/quotesProducts/{id_quote}', function (Request $request, Response $response, $args) use ($quotesDao, $quoteProductsDao) {
    $quotesProducts = $quoteProductsDao->findAllQuotesProductsByIdQuote($args['id_quote']);
    $response->getBody()->write(json_encode($quotesProducts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addQuote', function (Request $request, Response $response, $arsg) use (
    $quotesDao,
    $quoteProductsDao,
    $lastDataDao,
    $generalQuotesDao,
    $convertDataDao
) {
    $dataQuote = $request->getParsedBody();

    $quote = $quotesDao->insertQuote($dataQuote);

    if ($quote == null) {
        /* Obtener id cotizacion */
        $quote = $lastDataDao->findLastQuote();

        $products = $dataQuote['products'];

        /* Inserta todos los productos de la cotizacion */
        for ($i = 0; $i < sizeof($products); $i++) {
            $products[$i] = $convertDataDao->strReplaceQuotes($products[$i]);

            $quotesProducts = $quoteProductsDao->insertQuotesProducts($products[$i], $quote['id_quote']);
        }

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

$app->post('/updateQuote', function (Request $request, Response $response, $args) use ($quotesDao, $quoteProductsDao, $convertDataDao) {
    $dataQuote = $request->getParsedBody();

    $quote = $quotesDao->updateQuote($dataQuote);

    if ($quote == null)
        /* Elimina todos los productos de la cotizacion */
        $quotesProducts = $quoteProductsDao->deleteQuotesProducts($dataQuote['idQuote']);

    if ($quotesProducts == null) {
        /* Inserta todos los productos de la cotizacion */
        $products = $dataQuote['products'];

        for ($i = 0; $i < sizeof($products); $i++) {
            $product = $convertDataDao->strReplaceQuotes($products[$i]);

            $quotesProducts = $quoteProductsDao->insertQuotesProducts($product, $dataQuote['idQuote']);
        }
    }

    if ($quotesProducts == null)
        $resp = array('success' => true, 'message' => 'Cotizacion modificada correctamente');
    else if (isset($quotesProducts['info']))
        $resp = array('info' => true, 'message' => $quotesProducts['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteQuote/{id_quote}', function (Request $request, Response $response, $args) use ($quotesDao, $quoteProductsDao) {
    /* Elimina todos los productos de la cotizacion */
    $quotesProducts = $quoteProductsDao->deleteQuotesProducts($args['id_quote']);

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

$app->post('/sendQuote', function (Request $request, Response $response, $args) use ($generalQuotesDao, $sendMakeEmailDao, $sendEmailDao) {
    session_start();
    $email = $_SESSION['email'];

    $dataQuote = $request->getParsedBody();

    $dataQuote = $sendMakeEmailDao->SendEmailQuote($dataQuote, $email);

    $sendEmail = $sendEmailDao->sendEmail($dataQuote);

    if ($sendEmail == null)
        $quote = $generalQuotesDao->updateFlagQuote($dataQuote);

    if ($quote == null)
        $resp = array('success' => true, 'message' => 'Email de cotización enviada correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al enviar el email. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
