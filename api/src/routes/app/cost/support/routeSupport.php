<?php

use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\SendMakeEmailDao;
use tezlikv3\dao\WebTokenDao;

$sendMakeEmailDao = new SendMakeEmailDao();
$sendEmailDao = new SendEmailDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/sendEmailSupport', function (Request $request, Response $response, $args) use (
    $sendMakeEmailDao,
    $sendEmailDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $dataSupport = $request->getParsedBody();
    // session_start();
    $email = $_SESSION['email'];

    if (empty($dataSupport['subject']) || empty($dataSupport['message'])) {
        $resp = array('error' => true, 'message' => 'Porfavor ingrese todos los campos');
    } else {
        $dataEmail = $sendMakeEmailDao->sendEmailSupport($dataSupport, $email);

        $support = $sendEmailDao->sendEmail($dataEmail, 'soporteTezlik@tezliksoftware.com.co', 'SoporteTezlik');

        if ($support == null)
            $resp = array('success' => true, 'message' => 'Email enviado correctamente. Nos comunicaremos muy p ronto');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al enviar el correo. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
