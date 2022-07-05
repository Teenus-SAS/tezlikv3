<?php

use tezlikv3\dao\CostUserAccessDao;

$userAccessDao = new CostUserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta para acceso de todos los usuarios */

$app->get('/costUsersAccess', function (Request $request, Response $response, $args) use ($userAccessDao) {
    session_start();
    $company = $_SESSION['id_company'];
    $usersAccess = $userAccessDao->findAllUsersAccess($company);
    $response->getBody()->write(json_encode($usersAccess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/costUserAccess', function (Request $request, Response $response, $args) use ($userAccessDao) {
    session_start();
    $company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];
    $userAccess = $userAccessDao->findUserAccess($company, $id_user);
    $userAccess = $userAccess[0];
    $response->getBody()->write(json_encode($userAccess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCostUserAccess', function (Request $request, Response $response, $args) use ($userAccessDao) {
    session_start();
    $dataUserAccess = $request->getParsedBody();
    $id_user = $_SESSION['idUser'];

    if (
        empty($dataUserAccess['createProduct']) && empty($dataUserAccess['createMaterials']) &&
        empty($dataUserAccess['createMachines']) && empty($dataUserAccess['createProcess'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $userAccess = $userAccessDao->insertUserAccessByUser($dataUserAccess, $id_user);

        if ($userAccess == null)
            $resp = array('success' => true, 'message' => 'Acceso de usuario creado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCostUserAccess', function (Request $request, Response $response, $args) use ($userAccessDao) {
    session_start();
    $dataUserAccess = $request->getParsedBody();

    $userAccess = $userAccessDao->updateUserAccessByUsers($dataUserAccess);

    if ($userAccess == null)
        $resp = array('success' => true, 'message' => 'Acceso de usuario actualizado correctamente');
    elseif ($userAccess == 1)
        $resp = array('error' => true, 'message' => 'No puede actualizar este usuario');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// $app->post('/updateCostUserAccess', function (Request $request, Response $response, $args) use ($userAccessDao) {
//     session_start();
//     $dataUserAccess = $request->getParsedBody();
//     $idUser = $_SESSION['idUser'];

//     if ($dataUserAccess['idUser'] != $idUser) {

//         $userAccess = $userAccessDao->updateUserAccessByUsers($dataUserAccess);
//         if ($userAccess == null)
//             $resp = array('success' => true, 'message' => 'Acceso de usuario actualizado correctamente');
//         else
//             $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
//     } else {
//         $dataUserAccess['user'] = 1;
//         $userAccess = $userAccessDao->updateUserAccessByUsers($dataUserAccess);
//         $resp = array('success' => true, 'message' => 'Acceso de usuario actualizado correctamente, no puede quitar permisos de usuario.');
//     }

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });
