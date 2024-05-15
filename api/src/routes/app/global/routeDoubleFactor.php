<?php

use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\WebTokenDao;

//$sendCodeDao = new SendCodeDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Verificar codigo */

$app->post('/checkCode', function (Request $request, Response $response, $args) use (
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
    $code = $_SESSION['code'];
    $dataCheck = $request->getParsedBody();

    if ($dataCheck['code'] == $code)
        $resp = array('success' => true, 'message' => 'Inicio de sesión completado');
    else
        $resp = array('error' => true, 'message' => 'Codigo incorrecto, ingrese el código nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
