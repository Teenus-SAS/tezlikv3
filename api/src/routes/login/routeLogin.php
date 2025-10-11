<?php

/**
 * ============================================
 * RUTAS DE LOGIN 
 * ============================================
 */

use tezlikv3\dao\{
    AutenticationUserDao,
    ContractDao,
    FirstLoginDao,
    GeneralUserAccessDao,
    LicenseCompanyDao,
    StatusActiveUserDao,
    GenerateCodeDao,
    HistoricalProductsDao,
    HistoricalUsersDao,
    SendEmailDao,
    LastLoginDao
};

use App\Auth\JWTManagerDao;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/login', function (RouteCollectorProxy $group) {

    $group->post('/userAutentication', function (Request $request, Response $response, $args) {

        $licenseDao = new LicenseCompanyDao();
        $autenticationDao = new AutenticationUserDao();
        $statusActiveUserDao = new StatusActiveUserDao();
        $lastLoginDao = new LastLoginDao();
        $userAccessDao = new GeneralUserAccessDao();
        $historicalUsersDao = new HistoricalUsersDao();
        $contractsDao = new ContractDao();
        $historicalProductsDao = new HistoricalProductsDao();

        $jwtSecret = $_ENV['jwt_key'] ?? '';
        $jwt = new JWTManagerDao($jwtSecret);

        $parsedBody = $request->getParsedBody();

        // Validar campos requeridos
        if (empty($parsedBody["validation-email"]) || empty($parsedBody["validation-password"])) {
            $resp = ['error' => true, 'message' => 'Email y contraseña son requeridos'];
            return ResponseHelper::withJson($response, $resp, 400);
        }

        $email = filter_var($parsedBody["validation-email"], FILTER_SANITIZE_EMAIL);
        $password = $parsedBody["validation-password"];
        $user = $autenticationDao->findByEmail($email);

        // Respuesta genérica para evitar enumeración de usuarios
        $genericError = ['error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente'];

        if ($user == null)
            return ResponseHelper::withJson($response, $genericError, 200);

        if (!password_verify($password, $user['password']))
            return ResponseHelper::withJson($response, $genericError, 200);

        // Configuración robusta de la sesión
        $sessionConfig = [
            'cookie_lifetime' => 86400,
            'cookie_secure' => isset($_SERVER['HTTPS']),
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true,
            'use_only_cookies' => true
        ];

        foreach ($sessionConfig as $key => $value)
            ini_set('session.' . $key, $value);

        if (session_status() === PHP_SESSION_NONE)
            session_start();

        session_regenerate_id(true);
        $_SESSION = [];

        if (empty($user['rol'])) {
            $dataCompany = $licenseDao->findLicenseCompany($user['id_company']);

            $today = date('Y-m-d');
            $licenseDay = $dataCompany['license_end'];
            $license = ($today < $licenseDay) ? 1 : 0;

            if ($license == 0) {
                return ResponseHelper::withJson($response, [
                    'error' => true,
                    'message' => 'Su licencia ha caducado, lo invitamos a comunicarse'
                ], 200);
            }

            if ($user['active'] != 1) {
                return ResponseHelper::withJson($response, [
                    'error' => true,
                    'message' => 'Usuario Inactivo, valide con el administrador'
                ], 200);
            }

            if ($user['session_active'] != 0) {
                return ResponseHelper::withJson($response, [
                    'error' => true,
                    'message' => 'Usuario logeado, cierre la sesión para abrir una nueva'
                ], 200);
            }

            $contract = $contractsDao->findContract();

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

            if (isset($dataCompany['cost_historical']) && $dataCompany['cost_historical'] === 1) {
                $_SESSION['historical_period'] = $dataCompany['historical_period'];
            }

            $userAccessDao->setGeneralAccess($user['id_user']);

            if (
                isset($_SESSION['historical']) && $_SESSION['historical'] == 1 &&
                isset($_SESSION['plan_cost_historical']) && $_SESSION['plan_cost_historical'] == 1
            ) {
                $historical = $historicalProductsDao->findLastHistorical($user['id_company']);
                $_SESSION['d_historical'] = 0;
                $_SESSION['date_product'] = 0;

                if ($historical && !isset($historical['info'])) {
                    $_SESSION['date_product'] = $historical['date_product'];
                    $_SESSION['d_historical'] = 1;
                }
            }

            if ($user['id_user'] != 1) {
                $historicalUsersDao->insertHistoricalUser($user['id_user']);
            }

            $location = '../../cost/';
        } else {
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

        // Generar token
        $token = $jwt->generateToken($_SESSION['idUser']);
        $jwt->setAuthCookie($token);
        $_SESSION['token'] = $token;

        // Actualizar último logueo
        $lastLoginDao->findLastLogin();

        // ✅ FIX CRÍTICO: ACTIVAR SESIÓN EN BD
        try {
            $success = $statusActiveUserDao->activateSession(
                (int)$_SESSION['idUser'],
                (int)$_SESSION['case']
            );

            if (!$success) {
                error_log("⚠️ No se pudo activar sesión en BD para usuario: " . $_SESSION['idUser']);
            }
        } catch (\Exception $e) {
            error_log("❌ ERROR al activar sesión en BD: " . $e->getMessage());
        }

        session_write_close();

        return ResponseHelper::withJson($response, [
            'success' => true,
            'location' => $location,
            'token' => $token,
            'companyConfigHistory' => $dataCompany['cost_historical'] ?? null,
            'updates_notice' => $user['updates_notice'] ?? null,
        ], 200);
    });


    $group->get('/logout', function (Request $request, Response $response, $args) {

        $statusActiveUserDao = new StatusActiveUserDao();
        $sessionWasActive = false;
        $userId = null;
        $case = null;

        try {
            // PASO 1: CAPTURAR DATOS DE SESIÓN
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (isset($_SESSION['idUser']) && isset($_SESSION['case'])) {
                $sessionWasActive = true;
                $userId = $_SESSION['idUser'];
                $case = $_SESSION['case'];

                error_log("🔓 Iniciando logout para " .
                    ($case == 1 ? 'usuario' : 'admin') .
                    " ID: $userId");
            }

            // PASO 2: DESACTIVAR EN BASE DE DATOS
            if ($sessionWasActive) {
                try {
                    $deactivationSuccess = $statusActiveUserDao->changeStatusUserLogin();

                    if ($deactivationSuccess) {
                        error_log("✅ Sesión desactivada en BD correctamente");
                    } else {
                        error_log("⚠️ changeStatusUserLogin retornó false, intentando método alternativo");

                        try {
                            $statusActiveUserDao->forceDeactivateById($userId, $case);
                            error_log("✅ Sesión desactivada usando método de respaldo");
                        } catch (\Exception $e) {
                            error_log("⚠️ Método de respaldo también falló: " . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    error_log("❌ ERROR en desactivación de BD: " . $e->getMessage());

                    if ($userId && $case) {
                        try {
                            $statusActiveUserDao->forceDeactivateById($userId, $case);
                            error_log("✅ Sesión desactivada con método de emergencia");
                        } catch (\Exception $e2) {
                            error_log("❌ Todos los métodos de desactivación fallaron");
                        }
                    }
                }
            }

            try {
                $_SESSION = [];

                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(
                        session_name(),
                        '',
                        time() - 42000,
                        $params["path"],
                        $params["domain"],
                        $params["secure"] ?? false,
                        $params["httponly"] ?? true
                    );
                }

                session_destroy();
                error_log("✅ Sesión PHP destruida correctamente");
            } catch (\Exception $e) {
                error_log("⚠️ Error al destruir sesión PHP: " . $e->getMessage());
            }

            try {
                $cookieParams = [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'domain' => $_SERVER['HTTP_HOST'] ?? '',
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ];

                setcookie('auth_token', '', $cookieParams);
                error_log("✅ Cookie de autenticación limpiada");
            } catch (\Exception $e) {
                error_log("⚠️ Error al limpiar cookie: " . $e->getMessage());
            }

            // PASO 5: RESPUESTA
            $response->getBody()->write(json_encode("1", JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            error_log("❌ ERROR CATASTRÓFICO en logout: " . $e->getMessage());

            try {
                @session_destroy();
            } catch (\Exception $e2) {
                // Ignorar
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Sesión cerrada (con advertencias)'
            ], JSON_NUMERIC_CHECK));

            return $response->withHeader('Content-Type', 'application/json');
        }
    })->add(new SessionMiddleware());

    $group->post('/saveFirstLogin', function (Request $request, Response $response, $args) {
        $firstLoginDao = new FirstLoginDao();

        try {
            $id_user = $_SESSION['idUser'];
            $dataUser = $request->getParsedBody();

            $firstLoginDao->saveDataUser($dataUser, $id_user);

            $_SESSION['name'] = $dataUser['firstname'];
            $_SESSION['lastname'] = $dataUser['lastname'];

            $resp = ['success' => true, 'message' => 'Usuario actualizado correctamente'];
            return ResponseHelper::withJson($response, $resp, 200);
        } catch (Exception $e) {
            error_log("Error en saveFirstLogin: " . $e->getMessage());
            return ResponseHelper::withJson($response, [
                'error' => true,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    })->add(new SessionMiddleware());
});
