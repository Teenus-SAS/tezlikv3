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

use App\Auth\JWTManagerDao;

$licenseDao = new LicenseCompanyDao();
$autenticationDao = new AutenticationUserDao();

$statusActiveUserDao = new StatusActiveUserDao();
$generateCodeDao = new GenerateCodeDao();
$sendEmailDao = new SendEmailDao();
$lastLoginDao = new LastLoginDao();
$userAccessDao = new GeneralUserAccessDao();
$historicalUsersDao = new HistoricalUsersDao();
$firstLoginDao = new FirstLoginDao();
$contractsDao = new ContractDao();
$historicalProductsDao = new HistoricalProductsDao();
$jwtSecret = $_ENV['jwt_key'] ?? '';
$jwt = new JWTManagerDao($jwtSecret);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;


/* Autenticación */

$app->post('/userAutentication', function (Request $request, Response $response, $args) use (
    $autenticationDao,
    $licenseDao,
    $statusActiveUserDao,
    $lastLoginDao,
    $userAccessDao,
    $historicalUsersDao,
    $contractsDao,
    $historicalProductsDao,
    $jwt
) {
    $parsedBody = $request->getParsedBody();

    // Validar campos requeridos
    if (empty($parsedBody["validation-email"]) || empty($parsedBody["validation-password"]))
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Email y contraseña son requeridos'], 400);

    $email = filter_var($parsedBody["validation-email"], FILTER_SANITIZE_EMAIL);
    $password = $parsedBody["validation-password"];
    $user = $autenticationDao->findByEmail($email);

    // Respuesta genérica para evitar enumeración de usuarios
    $genericError = ['error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente'];

    /* Usuario sin datos */
    if ($user == null)
        return ResponseHelper::withJson($response, $genericError, 200);

    /* Valida el password del usuario */
    if (!password_verify($password, $user['password']))
        return ResponseHelper::withJson($response, $genericError, 200);

    // Configuración robusta de la sesión
    $sessionConfig = [
        'cookie_lifetime' => 86400, // 1 día
        'cookie_secure' => isset($_SERVER['HTTPS']), // Solo en HTTPS
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
        'use_only_cookies' => true
    ];

    // Aplicar configuración de sesión
    foreach ($sessionConfig as $key => $value)
        ini_set('session.' . $key, $value);

    // Iniciar y asegurar la sesión
    if (session_status() === PHP_SESSION_NONE)
        session_start();

    // Regenerar ID de sesión para prevenir fixation
    session_regenerate_id(true);

    // Limpiar datos de sesión previos
    $_SESSION = [];

    if (empty($user['rol'])) {
        /* Validar licenciamiento empresa */
        $dataCompany = $licenseDao->findLicenseCompany($user['id_company']);

        $today = date('Y-m-d');
        $licenseDay = $dataCompany['license_end'];
        $today < $licenseDay ? $license = 1 : $license = 0;

        if ($license == 0)
            return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Su licencia ha caducado, lo invitamos a comunicarse'], 200);

        /* Validar que el usuario es activo */
        if ($user['active'] != 1)
            return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Usuario Inactivo, valide con el administrador'], 200);

        /* Valida la session del usuario */
        if ($user['session_active'] != 0)
            return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Usuario logeado, cierre la sesión para abrir una nueva'], 200);

        $contract = $contractsDao->findContract();

        /* Configurar datos de sesión */
        $_SESSION = [
            'active' => true,
            'idUser' => $user['id_user'],
            'case' => 1,
            'name' => $user['firstname'],
            'lastname' => $user['lastname'],
            'email' => $user['email'],
            'rol' => $user["id_rols"],
            'id_company' => $user['id_company'],
            'avatar' => $user['avatar'],
            'position' => $user['position'],
            'logoCompany' => $dataCompany['logo'],
            "time" => microtime(true),
            'plan' => $dataCompany['plan'],
            'license_days' => $dataCompany['license_days'],
            'status_historical' => 1,
            'coverage_usd' => $dataCompany['coverage_usd'],
            'coverage_eur' => $dataCompany['coverage_eur'],
            'deviation' => $dataCompany['deviation'],
            'demo' => 1,
            'd_contract' => $contract ? 1 : 0,
            'content' => $contract ? $contract['content'] : 0
        ];

        //valide si tiene activa la opcion de historico y almacene el tipo de periodo Semana o Año
        if ($dataCompany['cost_historical'] === 1)
            $_SESSION['historical_period'] = $dataCompany['historical_period'];

        // Guardar accesos de usuario 
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
        if ($user['id_user'] != 1) {
            $historicalUsersDao->insertHistoricalUser($user['id_user']);
        }
        $location = '../../cost/';
    } else {
        /* Configurar sesión admin */
        $_SESSION = [
            'active' => true,
            'idUser' => $user['id_admin'],
            'case' => 2,
            'name' => $user['firstname'],
            'lastname' => $user['lastname'],
            'email' => $user['email'],
            'avatar' => $user['avatar'],
            "time" => microtime(true)
        ];

        $location = '../../admin/';
    }

    // Generar token para el usuario
    $token = $jwt->generateToken($_SESSION['idUser']);

    // Establecer cookie
    $jwt->setAuthCookie($token);

    // Guardar en sesión
    $_SESSION['token'] = $token;

    /* Actualizar último logueo */
    $lastLoginDao->findLastLogin();

    /* Modificar el estado de la sesión del usuario en BD */
    $statusActiveUserDao->changeStatusUserLogin();

    // Forzar escritura y cierre de sesión
    session_write_close();

    return ResponseHelper::withJson($response, [
        'success' => true,
        'location' => $location,
        'token' => $token,
        'companyConfigHistory' => $dataCompany['cost_historical'] ?? null,
        'updates_notice' => $user['updates_notice'] ?? null,
    ], 200);
});

$app->post('/saveFirstLogin', function (Request $request, Response $response, $args) use ($firstLoginDao) {
    try {
        //Obtener data
        $id_user = $_SESSION['idUser'];
        $dataUser = $request->getParsedBody();

        //Almacenar data
        $firstLoginDao->saveDataUser($dataUser, $id_user);

        $_SESSION['name'] = $dataUser['firstname'];
        $_SESSION['lastname'] = $dataUser['lastname'];

        return ResponseHelper::withJson($response, ['success' => true, 'message' => 'Usuario actualizado correctamente'], 200);
    } catch (Exception $e) {
        error_log("Error en saveFirstLogin: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error interno del servidor'], 500);
    }
})->add(new SessionMiddleware());

/* Logout */
$app->get('/logout', function (Request $request, Response $response, $args) use ($statusActiveUserDao,) {
    $statusActiveUserDao->changeStatusUserLogin();
    session_destroy();
    $response->getBody()->write(json_encode("1", JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
