<?php

use tezlikv2\dao\SendEmailDao;

$sendEmailDao = new SendEmailDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/sendEmailSupport', function (Request $request, Response $response, $args) use ($sendEmailDao) {
    $dataSupport = $request->getParsedBody();
    session_start();
    $email = $_SESSION['email'];

    if (empty($dataSupport['ccHeader']) || empty($dataSupport['subject']) || empty($dataSupport['message'])) {
        $resp = array('error' => true, 'message' => 'Porfavor ingrese todos los campos');
    } else
        $support = $sendEmailDao->sendEmailSupport($dataSupport, $email);

    if ($support == null)
        $resp = array('success' => true, 'message' => 'El correo a sido enviado, espere una pronta respuesta');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al enviar el correo. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
