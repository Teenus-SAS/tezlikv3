<?php

use tezlikv3\dao\GeneralUserAccessDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\PlanningUserAccessDao;
use tezlikv3\dao\UsersDao;

$usersDao = new UsersDao();
$lastDataDao = new LastDataDao();
$userAccessDao = new PlanningUserAccessDao();
$generalUAccessDao = new GeneralUserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta para acceso de todos los usuarios */

$app->get('/planningUsersAccess', function (Request $request, Response $response, $args) use ($userAccessDao) {
    session_start();
    $company = isset($_SESSION['id_company']) ? $_SESSION['id_company'] : 0;
    $usersAccess = $userAccessDao->findAllUsersAccess($company);
    $response->getBody()->write(json_encode($usersAccess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/planningUserAccess', function (Request $request, Response $response, $args) use ($userAccessDao) {
    session_start();
    $company = isset($_SESSION['id_company']) ? $_SESSION['id_company'] : 0;
    $id_user = $_SESSION['idUser'];
    $userAccess = $userAccessDao->findUserAccess($company, $id_user);
    // $userAccess = $userAccess[0];
    $response->getBody()->write(json_encode($userAccess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanningUserAccess', function (Request $request, Response $response, $args) use ($userAccessDao, $lastDataDao, $generalUAccessDao, $usersDao) {
    session_start();
    $dataUserAccess = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    if (
        empty($dataUserAccess['planningCreateProduct']) && empty($dataUserAccess['planningCreateMaterials']) &&
        empty($dataUserAccess['planningCreateMachines']) && empty($dataUserAccess['planningCreateProcess'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {

        if (isset($dataUserAccess['idUser']))
            $user = $dataUserAccess;
        else {
            $user = $lastDataDao->findLastInsertedUser($id_company);
        }

        $userAccess = $userAccessDao->insertUserAccessByUser($dataUserAccess, $user['idUser']);

        /* Modificar accesos */
        $generalUAccessDao->setGeneralAccess($user['idUser']);

        if ($userAccess == null)
            $resp = array('success' => true, 'message' => 'Acceso de usuario creado correctamente');
        else if (isset($userAccess['info']))
            $resp = array('info' => true, 'message' => $userAccess['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanningUserAccess', function (Request $request, Response $response, $args) use ($userAccessDao, $lastDataDao, $generalUAccessDao, $usersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataUserAccess = $request->getParsedBody();

    $userAccess = $userAccessDao->updateUserAccessByUsers($dataUserAccess);

    // if (isset($dataUserAccess['idUser']))
    //     $user = $dataUserAccess;
    // else {
    //     $user = $lastDataDao->findLastInsertedUser($id_company);
    // }

    /* Modificar accesos */
    $generalUAccessDao->setGeneralAccess($dataUserAccess['idUser']);

    if ($userAccess == null)
        $resp = array('success' => true, 'message' => 'Acceso de usuario actualizado correctamente');
    elseif ($userAccess == 1)
        $resp = array('error' => true, 'message' => 'No puede actualizar este usuario');
    else if (isset($userAccess['info']))
        $resp = array('info' => true, 'message' => $userAccess['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
