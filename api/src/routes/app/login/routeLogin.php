<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\CostUserAccessDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\StatusActiveUserDao;
use tezlikv3\dao\GenerateCodeDao;
use tezlikv3\dao\SendEmailDao;
use tezlikv3\dao\LastLoginDao;
use tezlikv3\dao\PlanningUserAccessDao;

$licenseDao = new LicenseCompanyDao();
$autenticationDao = new AutenticationUserDao();
$statusActiveUserDao = new StatusActiveUserDao();
$generateCodeDao = new GenerateCodeDao();
$sendEmailDao = new SendEmailDao();
$lastLoginDao = new LastLoginDao();
$costUserAccessDao = new CostUserAccessDao();
$planningUserAccessDao = new PlanningUserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Autenticación */

$app->post('/userAutentication', function (Request $request, Response $response, $args) use (
    $autenticationDao,
    $licenseDao,
    $statusActiveUserDao,
    $generateCodeDao,
    $sendEmailDao,
    $lastLoginDao,
    $costUserAccessDao,
    $planningUserAccessDao
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

    if ($user['password'] !== hash("sha256", $password)) {
        // if (!password_verify($password, $user['password'])) {
        $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /* valide licenciamiento empresa */

    $license = $licenseDao->findLicense($user['id_company']);

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

    /* Nueva session */
    session_start();
    $_SESSION['active'] = true;
    $_SESSION['idUser'] = $user['id_user'];
    $_SESSION['name'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['rol'] = $user["id_rols"];
    $_SESSION['id_company'] = $user['id_company'];
    $_SESSION['avatar'] = $user['avatar'];
    $_SESSION["time"] = time();

    /* Actualizar metodo ultimo logueo */
    $lastLoginDao->findLastLogin();

    /* Genera codigo */
    //$code = $generateCodeDao->GenerateCode();
    //$_SESSION["code"] = $code;

    /* Envio el codigo por email */
    //$sendEmailDao->SendEmailCode($code, $user);

    /* Modificar el estado de la sesion del usuario en BD */
    //$statusActiveUserDao->changeStatusUserLogin();

    if ($user["id_rols"] == 1) $location = '../../admin/';
    else {
        /* Validar licencia y accesos de usuario */
        $dataCompany = $licenseDao->findCostandPlanning($user['id_company']);
        if ($dataCompany['cost'] == 1 && $dataCompany['planning'] == 1) $location = '../../selector/';
        else if ($dataCompany['cost'] == 1 && $dataCompany['planning'] == 0) {
            $userAccess = $costUserAccessDao->findUserAccess($user['id_company'], $user['id_user']);
            // Guardar accesos usuario
            $_SESSION['aProducts'] = $userAccess['create_product'];
            $_SESSION['aMaterials'] = $userAccess['create_materials'];
            $_SESSION['aMachines'] = $userAccess['create_machines'];
            $_SESSION['aProcess'] = $userAccess['create_process'];
            $_SESSION['aProductsMaterials'] = $userAccess['product_materials'];
            $_SESSION['aProductProcess'] = $userAccess['product_process'];
            $_SESSION['aFactoryLoad'] = $userAccess['factory_load'];
            $_SESSION['aExternalService'] = $userAccess['external_service'];
            $_SESSION['aPayroll'] = $userAccess['payroll_load'];
            $_SESSION['aExpense'] = $userAccess['expense'];
            $_SESSION['aExpenseDistribution'] = $userAccess['expense_distribution'];
            $_SESSION['aUser'] = $userAccess['user'];
            $_SESSION['aPrice'] = $userAccess['price'];
            $_SESSION['aAnalysisMaterials'] = $userAccess['analysis_material'];
            $_SESSION['aTool'] = $userAccess['tool'];

            $location = '../../cost/';
        } else if ($dataCompany['cost'] == 0 && $dataCompany['planning'] == 1) {
            $userAccess = $planningUserAccessDao->findUserAccess($user['id_company'], $user['id_user']);
            $location = 'planning';
        }
    }

    $resp = array('success' => true, 'message' => 'Ingresar código', 'location' => $location);
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    /* $resp = array('success' => true, 'message' => 'access granted');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json'); */
});

/* Logout */

$app->get('/logout', function (Request $request, Response $response, $args) use ($statusActiveUserDao) {
    session_start();
    //$statusActiveUserDao->changeStatusUserLogin();
    session_destroy();
    $response->getBody()->write(json_encode("1", JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
