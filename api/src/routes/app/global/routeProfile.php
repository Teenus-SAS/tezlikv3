<?php

use tezlikv3\dao\UsersDao;
use tezlikv3\dao\ProfileDao;

$usersDao = new UsersDao();
$profileDao = new ProfileDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/profile', function (Request $request, Response $response, $args) use ($usersDao) {
    $profile = $usersDao->findUser();
    $response->getBody()->write(json_encode($profile, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProfile', function (Request $request, Response $response, $args) use ($profileDao) {
    $dataUser = $request->getParsedBody();

    $profile = $profileDao->updateProfile($dataUser);

    if ($profile == null)
        $resp = array('success' => true, 'message' => 'Perfil actualizado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
