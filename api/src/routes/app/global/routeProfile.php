<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\ProfileDao;

$profileDao = new ProfileDao();
$usersDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/updateProfile', function (Request $request, Response $response, $args) use ($profileDao, $usersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataUser = $request->getParsedBody();

    $profile = $profileDao->updateProfile($dataUser);

    if (sizeof($_FILES) > 0) $profileDao->avatarUser($dataUser['idUser'], $id_company);

    if ($profile == null) {
        $user = $usersDao->findByEmail($dataUser['emailUser']);

        $_SESSION['name'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['avatar'] = $user['avatar'];

        $resp = array('success' => true, 'message' => 'Perfil actualizado correctamente');
    } else if (isset($profile['info']))
        $resp = array('info' => true, 'message' => $profile['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});