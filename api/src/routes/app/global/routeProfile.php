<?php

use tezlikv3\dao\ProfileDao;

$profileDao = new ProfileDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/updateProfile', function (Request $request, Response $response, $args) use ($profileDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataUser = $request->getParsedBody();

    $profile = $profileDao->updateProfile($dataUser);

    if (sizeof($_FILES) > 0) $profileDao->avatarUser($dataUser['idUser'], $id_company);


    if ($profile == null)
        $resp = array('success' => true, 'message' => 'Perfil actualizado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
