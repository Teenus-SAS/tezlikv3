<?php

use tezlikv3\dao\CompaniesDao;
use tezlikv3\dao\CompaniesLicenseDao;
use tezlikv3\dao\UsersDao;
use tezlikv3\dao\CostUserAccessDao;
use tezlikv3\dao\GeneralCostUserAccessDao;
use tezlikv3\dao\GeneralUsersDao;
use tezlikv3\dao\GenerateCodeDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\SendMakeEmailDao;
use tezlikv3\dao\WebTokenDao;

$webTokenDao = new WebTokenDao();
$userDao = new UsersDao();
$generateCodeDao = new GenerateCodeDao();
$makeEmailDao = new SendMakeEmailDao();
$sendEmailDao = new SendEmailDao();
$generalUsersDao = new GeneralUsersDao();
$costAccessUserDao = new CostUserAccessDao();
$generalCostUserAccessDao = new GeneralCostUserAccessDao();
$companyDao = new CompaniesDao();
$companiesLicenseDao = new CompaniesLicenseDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/users', function (Request $request, Response $response, $args) use (
    $userDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $company = isset($_SESSION['id_company']) ? $_SESSION['id_company'] : 0;
    // $resp = [
    //     $users,
    //     'validate' => $validate
    // ];

    $users = $userDao->findAllusersByCompany($company);
    $response->getBody()->write(json_encode($users, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/user', function (Request $request, Response $response, $args) use (
    $userDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $email = $_SESSION['email'];
    $users = $userDao->findUser($email);
    $response->getBody()->write(json_encode($users, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// Insertar usuario

$app->post('/addUser', function (Request $request, Response $response, $args) use (
    $userDao,
    $webTokenDao,
    $generateCodeDao,
    $makeEmailDao,
    $sendEmailDao,
    $generalUsersDao,
    $costAccessUserDao,
    $companiesLicenseDao,
    $generalCostUserAccessDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    //data
    $dataUser = $request->getParsedBody();

    !isset($_SESSION['id_company']) ? $id_company = $dataUser['company'] : $id_company = $_SESSION['id_company'];

    //selecciona quantity_user de companies_licenses que tengan el id_company
    $quantityAllowsUsers = $generalUsersDao->quantityUsersAllows($id_company);

    //obtener cantidad de usuarios creados con el id_company
    $quantityCreatedUsers = $generalUsersDao->quantityUsersCreated($id_company);

    if ($quantityAllowsUsers['quantity_user'] <= $quantityCreatedUsers['quantity_users'])
        $resp = array('error' => true, 'message' => 'Cantidad de usuarios maxima alcanzada');
    else {
        if (empty($dataUser['nameUser']) && empty($dataUser['lastnameUser']) && empty($dataUser['emailUser'])) {
            $resp = array('error' => true, 'message' => 'Complete todos los datos');
        } else {
            $users = $userDao->findUser($dataUser['emailUser']);

            if ($users == false) {
                $email = $_SESSION['email'];
                $name = $_SESSION['name'];

                $newPass = $generateCodeDao->GenerateCode();

                // Se envia email con usuario(email) y contraseña
                $dataEmail = $makeEmailDao->SendEmailPassword($dataUser['emailUser'], $newPass);

                $sendEmail = $sendEmailDao->sendEmail($dataEmail, $email, $name);

                if (!isset($sendEmail['info'])) {
                    $pass = password_hash($newPass, PASSWORD_DEFAULT);

                    /* Almacena el usuario */
                    $users = $userDao->saveUser($dataUser, $pass, $id_company);

                    if ($users == null) {
                        $user = $userDao->findUser($dataUser['emailUser']);
                        $dataUser['id_user'] = $user['id_user'];

                        if (sizeof($dataUser['typeCustomPrices']) == 1)
                            $typeCustomPrice = $dataUser['typeCustomPrices'][0];
                        else
                            $typeCustomPrice = implode(',', $dataUser['typeCustomPrices']);

                        $usersAccess = $costAccessUserDao->insertUserAccessByUser($dataUser, $typeCustomPrice);

                        // if ($dataUser['typeExpenses'] != 0) {
                        //     $companiesLicenseDao->changeFlagExpense($dataUser, $id_company);
                        // }

                        if ($usersAccess == null && isset($dataUser['check']) && $dataUser['check'] == '1')
                            $usersAccess = $generalCostUserAccessDao->changePrincipalUser($dataUser);
                    }
                }
            } else $users = 1;
        }

        if ($users == 1) {
            $resp = array('error' => true, 'message' => 'El email ya se encuentra registrado. Intente con uno nuevo');
        } elseif ($users == null && $usersAccess == null) {
            $resp = array('success' => true, 'message' => 'Usuario creado correctamente'/*, 'pass' => $newPass*/);
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        }
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// Nuevo Usuario y Compañia Demo a traves de Web Teenus
$app->get('/newUserAndCompany/{email}', function (Request $request, Response $response, $args) use (
    $userDao,
    $webTokenDao,
    $generateCodeDao,
    $costAccessUserDao,
    $companyDao,
    $companiesLicenseDao,
    $lastDataDao,
    $makeEmailDao,
    $sendEmailDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    try {
        $resolution = [];

        $resolution = $userDao->findUser($args['email']);

        if ($resolution == false) {
            // Creacion compañia
            $resolution = $companyDao->addCompanyDemo();

            if ($resolution == null)
                $lastId = $lastDataDao->findLastCompany();
            if ($resolution == null) {
                /* Agregar datos a companies licenses */
                $dataCompany['license_start'] = '';
                $resolution = $companiesLicenseDao->addLicense($dataCompany, $lastId['idCompany']);
            }

            // Creacion de usuario
            $newPass = $generateCodeDao->GenerateCode();

            // Se envia email con usuario(email) y contraseña
            $dataEmail = $makeEmailDao->SendEmailPassword($args['email'], $newPass);

            $resolution = $sendEmailDao->sendEmail($dataEmail, 'soporteTezlik@tezliksoftware.com.co', 'SoporteTezlik');

            // if (!$resolution['info']) {
            $pass = password_hash($newPass, PASSWORD_DEFAULT);

            /* Almacena el usuario */
            $resolution = $userDao->saveUserOnlyEmail($args['email'], $pass, $lastId['idCompany']);

            if ($resolution == null) {
                $user = $userDao->findUser($args['email']);
                $dataUser = $costAccessUserDao->setDataUserAccessDemo($user['id_user']);

                $resolution = $costAccessUserDao->insertUserAccessByUser($dataUser, -1);
            }
            // }
        } else $resolution = 1;

        if ($resolution == 1) {
            $resp = array('error' => true, 'message' => 'El email ya se encuentra registrado. Intente con uno nuevo');
        } elseif ($resolution == null) {
            $resp = array('success' => true, 'message' => 'Usuario creado correctamente', 'pass' => $newPass);
        } elseif (isset($resolution['info'])) {
            $resp = array('info' => true, 'message' => $resolution['message']);
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        }
    } catch (\Exception $e) {
        $message = $e->getMessage();
        $resp = array('info' => true, 'message' => $message);
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUser', function (Request $request, Response $response, $args) use (
    $userDao,
    $webTokenDao,
    $generalCostUserAccessDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $dataUser = $request->getParsedBody();

    if (empty($dataUser['nameUser']) && empty($dataUser['lastnameUser'])) {
        $resp = array('error' => true, 'message' => 'Ingrese sus Nombres y Apellidos completos');
    } else {
        $users = $userDao->updateUser($dataUser, null);

        if ($users == null && isset($dataUser['check']) && $dataUser['check'] == '1')
            $users = $generalCostUserAccessDao->changePrincipalUser($dataUser);
    }
    if ($users == null)
        $resp = array('success' => true, 'message' => 'Usuario actualizado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error, Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteUser', function (Request $request, Response $response, $args) use (
    $userDao,
    $webTokenDao,
    $costAccessUserDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $dataUser = $request->getParsedBody();
    // session_start();
    $idUser = $_SESSION['idUser'];

    if ($dataUser['id_user'] != $idUser) {
        $usersAccess = $costAccessUserDao->deleteUserAccess($dataUser);

        if ($usersAccess == null)
            $users = $userDao->deleteUser($dataUser);

        if ($users == null && $usersAccess == null)
            $resp = array('success' => true, 'message' => 'Usuario eliminado correctamente');
        else if (isset($users['info']))
            $resp = array('info' => true, 'message' => $users['message']);
        else
            $resp = array('error' => true, 'message' => 'No fue posible eliminar el usuario, Intente nuevamente');
    } else {
        $resp = array('error' => true, 'message' => 'No es posible eliminar este usuario');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/changePrincipalUser', function (Request $request, Response $response, $args) use (
    $generalCostUserAccessDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $dataUserAdmin = $request->getParsedBody();

    $user = $generalCostUserAccessDao->changePrincipalUser($dataUserAdmin);

    if ($user == null)
        $resp = array('success' => true, 'message' => 'Usuario principal guardado correctamente');
    else if (isset($user['info']))
        $resp = array('info' => true, 'message' => $user['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// Activar/Inactivar Usuario
$app->get('/changeActiveUser/{id_user}/{op}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalUsersDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $resolution = $generalUsersDao->inactivateActivateUser($args['id_user'], $args['op']);

    $args['op'] == '1' ? $msg = 'activado' : $msg = 'inactivado';

    if ($resolution == null)
        $resp = array('success' => true, 'message' => "Usuario $msg correctamente");
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
