<?php
// api/src/Middleware/SessionMiddleware.php

namespace App\Middleware;

use tezlikv3\dao\StatusActiveUserDao;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use App\Helpers\ResponseHelper;
use DateTimeImmutable;


class SessionMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Configuración de sesión
        $this->configureSession();

        // Verificar sesión activa
        if (empty($_SESSION['active']) || empty($_SESSION['idUser'])) {
            $this->deactivateUserSession();
            return $this->unauthorizedResponse('Debes iniciar sesión primero');
        }

        // Verificar token JWT
        $token = $_SESSION['token'] ?? null;
        if (!$token) {
            $this->deactivateUserSession();
            return $this->unauthorizedResponse('Token no encontrado');
        }

        try {
            // DECODIFICACIÓN Y VALIDACIÓN DEL TOKEN
            $key = $_ENV['jwt_key'];
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // 1. Verificar coincidencia con usuario en sesión
            $tokenUserId = is_object($decoded->data) ? $decoded->data->userId : $decoded->data;
            if ($tokenUserId != $_SESSION['idUser']) {
                $this->deactivateUserSession();
                throw new \Exception('Token no coincide con el usuario');
            }

            // 2. Verificar tiempo de expiración (nueva verificación)
            $now = new DateTimeImmutable();
            if ($decoded->exp < $now->getTimestamp()) {
                $this->deactivateUserSession();
                throw new \Exception('Token expirado');
            }

            // 3. Renovar token si está cerca de expirar (15 minutos antes)
            if (($decoded->exp - $now->getTimestamp()) < 900) {
                $userId = is_object($decoded->data) ? $decoded->data->userId : $decoded->data;
                $this->refreshToken((int)$userId); // Aseguramos tipo int
            }

            return $handler->handle($request);
        } catch (\Exception $e) {
            $this->deactivateUserSession();
            return $this->unauthorizedResponse('Token inválido: ' . $e->getMessage());
        }
    }

    private function configureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400, // 1 día
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }
    }

    private function refreshToken(int $userId): void
    {
        $key = $_ENV['jwt_key'];
        $expiration = strtotime('+30 minutes');

        $payload = [
            'exp' => $expiration,
            'data' => $userId,
            'iat' => time()
        ];

        $newToken = JWT::encode($payload, $key, 'HS256');
        $_SESSION['token'] = $newToken;

        setcookie(
            'auth_token',
            $newToken,
            [
                'expires' => $expiration,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }

    private function unauthorizedResponse(string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        return ResponseHelper::withJson($response, [
            'error' => true,
            'message' => $message,
            'reload' => true
        ], 401);
    }

    /**
     * Maneja el caso cuando desactivar usuario de la BD
     */
    private function deactivateUserSession(): void
    {
        try {
            // Validar que existan los datos necesarios
            if (empty($_SESSION['idUser']) || empty($_SESSION['idCompany'])) {
                error_log("Sesión sin datos válidos para desactivar");
                return;
            }

            $id_user = $_SESSION['idUser'];
            $id_company = $_SESSION['idCompany'];

            $statusActiveUserDao = new StatusActiveUserDao();
            $statusActiveUserDao->deactivateSession($id_company, $id_user);

            // Limpiar datos de sesión
            session_unset();
            session_destroy();
        } catch (\Exception $e) {
            error_log("Error actualizando estado por expiración: " . $e->getMessage());
        }
    }
}
