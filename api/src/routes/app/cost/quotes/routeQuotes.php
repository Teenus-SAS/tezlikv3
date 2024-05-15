<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\GeneralQuotesDao;
use tezlikv3\dao\FilesDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\QuoteProductsDao;
use tezlikv3\dao\QuotesDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\SendMakeEmailDao;
use tezlikv3\dao\WebTokenDao;

$quotesDao = new QuotesDao();
$quoteProductsDao = new QuoteProductsDao();
$webTokenDao = new WebTokenDao();
$lastDataDao = new LastDataDao();
$generalQuotesDao = new GeneralQuotesDao();
$convertDataDao = new ConvertDataDao();
$FilesDao = new FilesDao();
$sendEmailDao = new SendEmailDao();
$sendMakeEmailDao = new SendMakeEmailDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consultar Todos */

$app->get('/quotes', function (Request $request, Response $response, $args) use (
    $quotesDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $quotes = $quotesDao->findAllQuotes($id_company);
    $response->getBody()->write(json_encode($quotes, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Clonar cotización */
$app->get('/copyQuote/{id_quote}', function (Request $request, Response $response, $args) use (
    $quotesDao,
    $webTokenDao,
    $quoteProductsDao,
    $lastDataDao,
    $convertDataDao,
    $generalQuotesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    // $flag_indirect = $_SESSION['flag_indirect'];
    $quote = $quotesDao->findQuote($args['id_quote']);

    $dataQuote['company'] = $quote['id_company'];
    $dataQuote['contact'] = $quote['id_contact'];
    $dataQuote['idPaymentMethod'] = $quote['id_payment_method'];
    $dataQuote['offerValidity'] = $quote['offer_validity'];
    $dataQuote['warranty'] = $quote['warranty'];
    $dataQuote['deliveryDate'] = $quote['delivery_date'];
    $dataQuote['observation'] = $quote['observation'];
    // $dataQuote['products'] = $products;

    $respquote = $quotesDao->insertQuote($dataQuote, $id_company);

    // if ($flag_indirect == '0')
    //     $products = $quoteProductsDao->findAllQuotesProductsByIdQuote($args['id_quote']);
    // else
    $products = $generalQuotesDao->findAllQuotesProductsAndMaterialsByIdQuote($args['id_quote']);

    $lastQuote = $lastDataDao->findLastQuote();

    for ($i = 0; $i < sizeof($products); $i++) {
        $products[$i] = $convertDataDao->strReplaceQuotes($products[$i]);
        $resp = $quoteProductsDao->insertQuotesProducts($products[$i], $lastQuote['id_quote']);
    }

    if ($respquote == null && $resp == null)
        $resp = array('success' => true, 'message' => 'Cotización copiada correctamente');
    else
        $resp = array('error' => true, 'message' => 'La Cotización no puede ser copiada, Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar detalle de cotizacion */
$app->get('/quote/{id_quote}', function (Request $request, Response $response, $args) use (
    $quotesDao,
    $webTokenDao,
    $generalQuotesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $quote = $quotesDao->findQuote($args['id_quote']);
    $quotesProducts = $generalQuotesDao->findAllQuotesProductsAndMaterialsByIdQuote($args['id_quote']);

    $data['quote'] = $quote;
    $data['quotesProducts'] = $quotesProducts;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/quotesProducts/{id_quote}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalQuotesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $quotesProducts = $generalQuotesDao->findAllQuotesProductsAndMaterialsByIdQuote($args['id_quote']);

    $response->getBody()->write(json_encode($quotesProducts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addQuote', function (Request $request, Response $response, $arsg) use (
    $quotesDao,
    $webTokenDao,
    $quoteProductsDao,
    $lastDataDao,
    $convertDataDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataQuote = $request->getParsedBody();

    $resolution = $quotesDao->insertQuote($dataQuote, $id_company);

    if ($resolution == null) {
        /* Obtener id cotizacion */
        $quote = $lastDataDao->findLastQuote();

        $products = $dataQuote['products'];

        /* Inserta todos los productos de la cotizacion */
        for ($i = 0; $i < sizeof($products); $i++) {
            $products[$i] = $convertDataDao->strReplaceQuotes($products[$i]);
            $resolution = $quoteProductsDao->insertQuotesProducts($products[$i], $quote['id_quote']);

            // if ($products[$i]['indirect'] == '0')
            //     $resolution = $quoteProductsDao->insertQuotesProducts($products[$i], $quote['id_quote']);
            // else {
            //     if ($i > 1) {
            //         $resolution = $quoteProductsDao->insertQuotesProducts($products[$i], $quote['id_quote']);
            //     }

            //     $quotesProduct = $lastDataDao->findLastQuoteProducts();

            //     $resolution = $generalQuotesDao->updateQuotesProducts($products[$i], $quotesProduct['id_quote_product']);
            // }

            if (isset($resolution['info'])) break;
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Cotización insertada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateQuote', function (Request $request, Response $response, $args) use (
    $quotesDao,
    $webTokenDao,
    $quoteProductsDao,
    $convertDataDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $dataQuote = $request->getParsedBody();

    $resolution = $quotesDao->updateQuote($dataQuote);

    if ($resolution == null)
        /* Elimina todos los productos de la cotizacion */
        $resolution = $quoteProductsDao->deleteQuotesProducts($dataQuote['idQuote']);

    if ($resolution == null) {
        /* Inserta todos los productos de la cotizacion */
        $products = $dataQuote['products'];

        for ($i = 0; $i < sizeof($products); $i++) {
            $products[$i] = $convertDataDao->strReplaceQuotes($products[$i]);

            $resolution = $quoteProductsDao->insertQuotesProducts($products[$i], $dataQuote['idQuote']);
            // if ($products[$i]['indirect'] == 0)
            //     $resolution = $quoteProductsDao->insertQuotesProducts($products[$i], $dataQuote['idQuote']);
            // else {
            //     // if ($i > 1) {
            //     $resolution = $quoteProductsDao->insertQuotesProducts($products[$i], $dataQuote['idQuote']);
            //     // }

            //     // $quotesProduct = $lastDataDao->findLastQuoteProducts();

            //     // $resolution = $generalQuotesDao->updateQuotesProducts($products[$i], $quotesProduct['id_quote_product']);
            // }

            if (isset($resolution['info'])) break;
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Cotizacion modificada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteQuote/{id_quote}', function (Request $request, Response $response, $args) use (
    $quotesDao,
    $quoteProductsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

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

$app->post('/sendQuote', function (Request $request, Response $response, $args) use (
    $generalQuotesDao,
    $webTokenDao,
    $sendMakeEmailDao,
    $FilesDao,
    $sendEmailDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $email = $_SESSION['email'];
    $name = $_SESSION['name'];
    $id_company = $_SESSION['id_company'];

    $dataQuote = $request->getParsedBody();

    $file = $FilesDao->uploadPDFQuote($id_company);

    $dataEmail = $sendMakeEmailDao->SendEmailQuote($dataQuote, $email, $file);

    $resolution = $sendEmailDao->sendEmail($dataEmail, $email, $name);

    if ($resolution == null)
        $resolution = $generalQuotesDao->updateFlagQuote($dataQuote);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Email de cotización enviada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al enviar el email. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
