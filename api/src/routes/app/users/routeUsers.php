<?php

use tezlikv3\dao\CompaniesDao;
use tezlikv3\dao\CompaniesLicenseDao;
use tezlikv3\dao\UsersDao;
// Cantidad de usuarios
use tezlikv3\dao\QuantityUsersDao;
//Acceso de usuario
use tezlikv3\dao\CostUserAccessDao;
use tezlikv3\dao\GenerateCodeDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\SendMakeEmailDao;

$userDao = new UsersDao();
$generateCodeDao = new GenerateCodeDao();
$makeEmailDao = new SendMakeEmailDao();
$sendEmailDao = new SendEmailDao();
$quantityUsersDao = new QuantityUsersDao();
$costAccessUserDao = new CostUserAccessDao();
$companyDao = new CompaniesDao();
$companiesLicenseDao = new CompaniesLicenseDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/users', function (Request $request, Response $response, $args) use ($userDao) {
    session_start();
    $company = $_SESSION['id_company'];
    $users = $userDao->findAllusersByCompany($company);
    $response->getBody()->write(json_encode($users, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/user', function (Request $request, Response $response, $args) use ($userDao) {
    session_start();
    $email = $_SESSION['email'];
    $users = $userDao->findUser($email);
    $response->getBody()->write(json_encode($users, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// Insertar usuario

$app->post('/addUser', function (Request $request, Response $response, $args) use (
    $userDao,
    $generateCodeDao,
    $makeEmailDao,
    $sendEmailDao,
    $quantityUsersDao,
    $costAccessUserDao
) {
    session_start();
    //data
    $dataUser = $request->getParsedBody();

    !isset($_SESSION['id_company']) ? $id_company = $dataUser['company'] : $id_company = $_SESSION['id_company'];

    //selecciona quantity_user de companies_licenses que tengan el id_company
    $quantityAllowsUsers = $quantityUsersDao->quantityUsersAllows($id_company);

    //obtener cantidad de usuarios creados con el id_company
    $quantityCreatedUsers = $quantityUsersDao->quantityUsersCreated($id_company);


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
                        $dataUser['idUser'] = $user['id_user'];

                        $usersAccess = $costAccessUserDao->insertUserAccessByUser($dataUser);
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
    $generateCodeDao,
    $costAccessUserDao,
    $companyDao,
    $companiesLicenseDao,
    $lastDataDao,
    $makeEmailDao,
    $sendEmailDao
) {
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

                $resolution = $costAccessUserDao->insertUserAccessByUser($dataUser);
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

$app->post('/updateUser', function (Request $request, Response $response, $args) use ($userDao, $costAccessUserDao) {
    session_start();
    $dataUser = $request->getParsedBody();

    !isset($_SESSION['id_company']) ? $id_company = $dataUser['company'] : $id_company = $_SESSION['id_company'];

    if (empty($dataUser['nameUser']) && empty($dataUser['lastnameUser'])) {
        $resp = array('error' => true, 'message' => 'Ingrese sus Nombres y Apellidos completos');
    } else {
        $users = $userDao->updateUser($dataUser, null);
        $usersAccess = $costAccessUserDao->insertUserAccessByUser($dataUser, $id_company);
    }
    if ($users == null && $usersAccess == null)
        $resp = array('success' => true, 'message' => 'Usuario actualizado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error, Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteUser', function (Request $request, Response $response, $args) use ($userDao, $costAccessUserDao) {
    $dataUser = $request->getParsedBody();
    session_start();
    $idUser = $_SESSION['idUser'];

    if ($dataUser['idUser'] != $idUser) {
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
