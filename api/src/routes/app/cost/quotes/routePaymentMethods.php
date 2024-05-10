<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\GeneralQuotesDao;
use tezlikv3\dao\PaymentMethodsDao;

$paymentMethodsDao = new PaymentMethodsDao();
$autenticationDao = new AutenticationUserDao();
$generalQuotesDao = new GeneralQuotesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/paymentMethods', function (Request $request, Response $response, $args) use (
    $paymentMethodsDao,
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

    session_start();
    $id_company = $_SESSION['id_company'];
    $paymentMethods = $paymentMethodsDao->findAllPaymentMethods($id_company);

    $response->getBody()->write(json_encode($paymentMethods, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPaymentMethod', function (Request $request, Response $response, $arsg) use (
    $paymentMethodsDao,
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

    session_start();
    $id_company = $_SESSION['id_company'];

    $dataMethod = $request->getParsedBody();

    if (empty($dataMethod['method']))
        $resp = array('error' => true, 'message' => 'Ingrese los campos');
    else {
        $paymentMethods = $paymentMethodsDao->insertPaymentMethod($dataMethod, $id_company);

        if ($paymentMethods == null)
            $resp = array('success' => true, 'message' => 'Metodo de pago insertado correctamente');
        else if (isset($paymentMethods['info']))
            $resp = array('info' => true, 'message' => $paymentMethods['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePaymentMethod', function (Request $request, Response $response, $arsg) use (
    $paymentMethodsDao,
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

    $dataMethod = $request->getParsedBody();

    if (empty($dataMethod['idMethod']) || empty($dataMethod['method']))
        $resp = array('error' => true, 'message' => 'Ingrese los campos');
    else {
        $paymentMethods = $paymentMethodsDao->updatePaymentMethod($dataMethod);

        if ($paymentMethods == null)
            $resp = array('success' => true, 'message' => 'Metodo de pago modificado correctamente');
        else if (isset($paymentMethods['info']))
            $resp = array('info' => true, 'message' => $paymentMethods['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePaymentMethod/{id_method}', function (Request $request, Response $response, $args) use (
    $paymentMethodsDao,
    $autenticationDao,
    $generalQuotesDao
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

    $quotes = $generalQuotesDao->findPaymentMethod($args['id_method']);

    if (sizeof($quotes) > 0)
        $paymentMethods = $paymentMethodsDao->changeFlagPaymentMethod($args['id_method'], 1);
    else
        $paymentMethods = $paymentMethodsDao->deletePaymentMethod($args['id_method']);

    if ($paymentMethods == null)
        $resp = array('success' => true, 'message' => 'Metodo de pago eliminado correctamente');
    else if (isset($paymentMethods['info']))
        $resp = array('info' => true, 'message' => $paymentMethods['message']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar la información');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
