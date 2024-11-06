<?php

use tezlikv3\dao\CompaniesLicenseDao;
use tezlikv3\dao\CostUserAccessDao;
use tezlikv3\dao\GeneralUserAccessDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\UsersDao;
use tezlikv3\dao\WebTokenDao;

$usersDao = new UsersDao();
$webTokenDao = new WebTokenDao();
$lastDataDao = new LastDataDao();
$userAccessDao = new CostUserAccessDao();
$generalUAccessDao = new GeneralUserAccessDao();
$companiesLicenseDao = new CompaniesLicenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta para acceso de todos los usuarios */

$app->get('/costUsersAccess', function (Request $request, Response $response, $args) use (
    $userAccessDao,
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
    $company = isset($_SESSION['id_company']) ? $_SESSION['id_company'] : 0;
    $usersAccess = $userAccessDao->findAllUsersAccess($company);
    $response->getBody()->write(json_encode($usersAccess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/costUserAccess', function (Request $request, Response $response, $args) use (
    $userAccessDao,
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
    $company = isset($_SESSION['id_company']) ? $_SESSION['id_company'] : 0;
    $id_user = $_SESSION['idUser'];
    $userAccess = $userAccessDao->findUserAccess($company, $id_user);
    // $userAccess = $userAccess[0];
    $response->getBody()->write(json_encode($userAccess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCostUserAccess', function (Request $request, Response $response, $args) use (
    $userAccessDao,
    $webTokenDao,
    $lastDataDao,
    $generalUAccessDao,
    $usersDao,
    $companiesLicenseDao
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
    $dataUserAccess = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    if (
        empty($dataUserAccess['createProduct']) && empty($dataUserAccess['costCreateMaterials']) &&
        empty($dataUserAccess['costCreateMachines']) && empty($dataUserAccess['costCreateProcess'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {

        if (isset($dataUserAccess['id_user']))
            $user = $dataUserAccess;
        else {
            $user = $lastDataDao->findLastInsertedUser($id_company);
        }

        $userAccess = $userAccessDao->insertUserAccessByUser($dataUserAccess, $id_company);

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

$app->post('/updateCostUserAccess', function (Request $request, Response $response, $args) use (
    $userAccessDao,
    $webTokenDao,
    $generalUAccessDao,
    $companiesLicenseDao
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
    $id_company = $_SESSION['id_company'];
    $idUser = $_SESSION['idUser'];

    $dataUserAccess = $request->getParsedBody();

    $findUserAccess = $userAccessDao->findUserAccess($id_company, $dataUserAccess['id_user']);

    if (sizeof($dataUserAccess['typeCustomPrices']) == 1)
        $typeCustomPrice = $dataUserAccess['typeCustomPrices'][0];
    else
        $typeCustomPrice = implode(',', $dataUserAccess['typeCustomPrices']);

    if ($findUserAccess)
        $userAccess = $userAccessDao->updateUserAccessByUsers($dataUserAccess, $typeCustomPrice);
    else
        $userAccess = $userAccessDao->insertUserAccessByUser($dataUserAccess, $typeCustomPrice);

    // if ($dataUserAccess['typeExpenses'] != 0) {
    //     $companiesLicenseDao->changeFlagExpense($dataUserAccess, $id_company);
    // }

    /* Modificar accesos */
    if ($idUser == $dataUserAccess['id_user'])
        $generalUAccessDao->setGeneralAccess($dataUserAccess['id_user']);

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
