<?php

use tezlikv3\dao\UserAdminDao;

$userAdminDao = new UserAdminDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/userAdmins', function (Request $request, Response $response, $args) use ($userAdminDao) {
    $userAdmin = $userAdminDao->findAllUserAdmins();
    $response->getBody()->write(json_encode($userAdmin, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addUserAdmin', function (Request $request, Response $response, $args) use ($userAdminDao) {
    $dataUserAdmin = $request->getParsedBody();

    if (empty($dataUserAdmin['email']) || empty($dataUserAdmin['password']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $userAdmin = $userAdminDao->insertUserAdmin($dataUserAdmin);

        if ($userAdmin == null)
            $resp = array('success' => true, 'message' => 'Usuario creado correctamente');
        else if (isset($userAdmin['info']))
            $resp = array('info' => true, 'message' => $userAdmin['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUserAdmin', function (Request $request, Response $response, $args) use ($userAdminDao) {
    session_start();
    $email = $_SESSION['email'];
    $dataUserAdmin = $request->getParsedBody();

    if (empty($dataUserAdmin['email']) || empty($dataUserAdmin['password']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {

        if ($dataUserAdmin['email'] == $email) {
            $resp = array('error' => true, 'message' => 'No puede actualizar este usuario');
            return false;
        }

        $userAdmin = $userAdminDao->updateUser($dataUserAdmin);

        if ($userAdmin == null)
            $resp = array('success' => true, 'message' => 'Usuario modificado correctamente');
        else if (isset($userAdmin['info']))
            $resp = array('info' => true, 'message' => $userAdmin['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteUserAdmin', function (Request $request, Response $response, $args) use ($userAdminDao) {
    session_start();
    $email = $_SESSION['email'];
    $dataUserAdmin = $request->getParsedBody();

    if ($dataUserAdmin['email'] == $email) {
        $resp = array('error' => true, 'message' => 'No puede eliminar este usuario');
        return false;
    }

    $userAdmin = $userAdminDao->deleteUser($dataUserAdmin['idAdmin']);

    if ($userAdmin == null)
        $resp = array('success' => true, 'message' => 'Usuario eliminado correctamente');
    else if (isset($userAdmin['info']))
        $resp = array('info' => true, 'message' => $userAdmin['info']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el usuario, existe información asociada a él');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});