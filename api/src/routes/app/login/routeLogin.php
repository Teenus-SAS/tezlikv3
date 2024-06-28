<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\ContactsDao;
use tezlikv3\dao\ContractDao;
use tezlikv3\dao\FirstLoginDao;
use tezlikv3\dao\GeneralUserAccessDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\StatusActiveUserDao;
use tezlikv3\dao\GenerateCodeDao;
use tezlikv3\dao\HistoricalProductsDao;
use tezlikv3\dao\HistoricalUsersDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\LastLoginDao;
use tezlikv3\dao\WebTokenDao;

$licenseDao = new LicenseCompanyDao();
$autenticationDao = new AutenticationUserDao();
$webTokenDao = new WebTokenDao();
$statusActiveUserDao = new StatusActiveUserDao();
$generateCodeDao = new GenerateCodeDao();
$sendEmailDao = new SendEmailDao();
$lastLoginDao = new LastLoginDao();
$userAccessDao = new GeneralUserAccessDao();
$historicalUsersDao = new HistoricalUsersDao();
$firstLoginDao = new FirstLoginDao();
$contractsDao = new ContractDao();
$historicalProductsDao = new HistoricalProductsDao();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Autenticación */

$app->post('/userAutentication', function (Request $request, Response $response, $args) use (
    $autenticationDao,
    $licenseDao,
    $statusActiveUserDao,
    $lastLoginDao,
    $userAccessDao,
    $historicalUsersDao,
    $contractsDao,
    $historicalProductsDao
) {
    $parsedBody = $request->getParsedBody();

    $user = $parsedBody["validation-email"];
    $password = $parsedBody["validation-password"];
    $user = $autenticationDao->findByEmail($user);

    $resp = array();

    /* Usuario sn datos */
    if ($user == null) {
        $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /* Valida el password del usuario */
    if (!password_verify($password, $user['password'])) {
        $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    if (empty($user['rol'])) {
        /* valide licenciamiento empresa */
        $dataCompany = $licenseDao->findLicenseCompany($user['id_company']);

        $today = date('Y-m-d');
        $licenseDay = $dataCompany['license_end'];
        $today < $licenseDay ? $license = 1 : $license = 0;

        if ($license == 0) {
            $resp = array('error' => true, 'message' => 'Su licencia ha caducado, lo invitamos a comunicarse');
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        /* Validar que el usuario es activo */

        if ($user['active'] != 1) {
            $resp = array('error' => true, 'message' => 'Usuario Inactivo, valide con el administrador');
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        /* Valida la session del usuario */

        if ($user['session_active'] != 0) {
            $resp = array('error' => true, 'message' => 'Usuario logeado, cierre la sesión para abrir una nueva');
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $contract = $contractsDao->findContract();

        /* Nueva session user */
        session_start();

        $_SESSION['d_contract'] = 0;
        $_SESSION['content'] = 0;

        if ($contract) {
            $_SESSION['content'] = $contract['content'];
            $_SESSION['d_contract'] = 1;
        }

        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $user['id_user'];
        $_SESSION['case'] = 1;
        $_SESSION['name'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = $user["id_rols"];
        $_SESSION['id_company'] = $user['id_company'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['position'] = $user['position'];
        $_SESSION['logoCompany'] = $dataCompany['logo'];
        $_SESSION["time"] = microtime(true);
        $_SESSION['plan'] = $dataCompany['plan'];
        $_SESSION['license_days'] = $dataCompany['license_days'];
        $_SESSION['status_historical'] = 1;
        $_SESSION['coverage_usd'] = $dataCompany['coverage_usd'];
        $_SESSION['coverage_eur'] = $dataCompany['coverage_eur'];
        $_SESSION['deviation'] = $dataCompany['deviation'];
        $_SESSION['demo'] = 1;

        // Guardar accesos de usario 
        $userAccessDao->setGeneralAccess($user['id_user']);

        if ($_SESSION['historical'] == 1 && $_SESSION['plan_cost_historical'] == 1) {
            $historical = $historicalProductsDao->findLastHistorical($user['id_company']);
            $_SESSION['d_historical'] = 0;
            $_SESSION['date_product'] = 0;

            if ($historical && !isset($historical['info'])) {
                $_SESSION['date_product'] = $historical['date_product'];
                $_SESSION['d_historical'] = 1;
            }
        }

        // Guardar sesion
        if ($user['id_user'] != 1)
            $historicalUsersDao->insertHistoricalUser($user['id_user']);
        $location = '../../cost/';
    } else {
        /* Nueva session admin*/
        session_start();
        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $user['id_admin'];
        $_SESSION['case'] = 2;
        $_SESSION['name'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION["time"] = microtime(true);

        $location = '../../admin/';
    }

    // $exp = strtotime('+1 hours');
    $exp = strtotime('+30 minutes');
    $key = $_ENV['jwt_key'];

    $payload = [
        'exp' => $exp,
        'data' => $_SESSION['idUser']
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');
    $_SESSION['token'] = $jwt;

    /* Actualizar metodo ultimo logueo */
    $lastLoginDao->findLastLogin();

    /* Modificar el estado de la sesion del usuario en BD */
    $statusActiveUserDao->changeStatusUserLogin();

    $resp = array('success' => true, 'message' => 'Ingresar código', 'location' => $location);
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveFirstLogin', function (Request $request, Response $response, $args) use (
    $firstLoginDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_user = $_SESSION['idUser'];
    $dataUser = $request->getParsedBody();

    $resolution = $firstLoginDao->saveDataUser($dataUser, $id_user);

    if ($resolution == null) {
        $resp = array('success' => true, 'message' => 'Usuario modificado correctamente');

        $_SESSION['name'] = $dataUser['firstname'];
        $_SESSION['lastname'] = $dataUser['lastname'];
    } else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error guardando la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/* Logout */
$app->get('/logout', function (Request $request, Response $response, $args) use (
    $statusActiveUserDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    // session_start();
    $statusActiveUserDao->changeStatusUserLogin();
    session_destroy();
    $response->getBody()->write(json_encode("1", JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
