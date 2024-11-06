<?php

use tezlikv3\dao\passUserDao;
use tezlikv3\dao\SendMakeEmailDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\WebTokenDao;

$passUserDao = new passUserDao();
$webTokenDao = new WebTokenDao();
$sendMakeEmailDao = new SendMakeEmailDao();
$sendEmailDao = new SendEmailDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Change Password */

$app->post('/changePassword', function (Request $request, Response $response, $args) use (
    $passUserDao,
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

    // session_start();

    if (isset($_SESSION['idUser'])) {
        $id = $_SESSION['idUser'];

        $parsedBody = $request->getParsedBody();
        $usersChangePassword = $passUserDao->ChangePasswordUser($id, $parsedBody["inputNewPass"]);

        if ($usersChangePassword == null)
            $resp = array('success' => true, 'message' => 'Cambio de Password correcto');
        else
            $resp = array('error' => true, 'message' => 'Hubo un problema, intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Usuario no autorizado');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/* Forgot Password */

$app->post('/forgotPassword', function (Request $request, Response $response, $args) use (
    $passUserDao,
    $webTokenDao,
    $sendEmailDao,
    $sendMakeEmailDao
) {
    // $info = $webTokenDao->getToken();

    // if (!is_object($info) && ($info == 1)) {
    //     $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    // }

    // if (is_array($info)) {
    //     $response->getBody()->write(json_encode(['error' => $info['info']]));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    // }

    // $validate = $webTokenDao->validationToken($info);

    // if (!$validate) {
    //     $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    // }

    $parsedBody = $request->getParsedBody();
    $email = trim($parsedBody["data"]);

    $passwordTemp = $passUserDao->forgotPasswordUser($email);

    if ($passwordTemp == null)
        $resp = array('success' => true, 'message' => 'La contraseña fue enviada al email suministrado exitosamente');
    else {
        $dataEmail = $sendMakeEmailDao->SendEmailForgotPassword($email, $passwordTemp);
        $email =  $sendEmailDao->SendEmail($dataEmail, 'soporteTezlik@tezliksoftware.com.co', 'SoporteTezlik');

        if ($email == null)
            $resp = array('success' => true, 'message' => "La contraseña fue enviada al email suministrado exitosamente.");
        else if (isset($email['info']))
            $resp = array('info' => true, 'message' => $email['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras enviaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
