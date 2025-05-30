<?php

namespace tezlikv3\dao;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;

class WebTokenDao_error
{
    private $logger;
    private $jwtSecret;
    private $jwtAlgorithm = 'HS256';

    // Inyecta el logger en el constructor
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    private function getSecretKey(): string
    {
        return $this->secretKey;
    }

    private function loadSecretKey(): string
    {
        // Opción 1: Desde variable de entorno (recomendado para producción)
        if (getenv('JWT_SECRET_KEY')) {
            return getenv('JWT_SECRET_KEY');
        }

        // Opción 2: Desde archivo de configuración
        if (file_exists(__DIR__ . '/../../config/jwt.php')) {
            $config = require __DIR__ . '/../../config/jwt.php';
            return $config['secret_key'];
        }

        // Opción 3: Clave por defecto (solo para desarrollo)
        return 'tu_clave_secreta_super_segura_aqui';
    }

    /**
     * Valida el token JWT
     */
    public function validateToken()
    {
        if (!isset($_SESSION['token'])) {
            // Usa error_log si el logger no está disponible
            $this->logError('Token not found in session');
            return ['error' => 'Token not found'];
        }

        try {
            $decoded = JWT::decode($_SESSION['token'], new Key($this->getSecretKey(), 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            $this->logError('Token validation failed: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function logError(string $message)
    {
        if ($this->logger !== null) {
            $this->logger->error($message);
        } else {
            // Fallback a error_log si no hay logger configurado
            error_log($message);
        }
    }


    /**
     * Inicia una sesión segura
     */
    public function startSecureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Determina el dominio dinámicamente
            $domain = $this->getCookieDomain();

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => $domain,
                'secure' => $this->isHttps(),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
            session_regenerate_id(true);
        }
    }

    private function getCookieDomain()
    {
        $host = $_SERVER['HTTP_HOST'];

        // Si es localhost o IP, no usar dominio (cookies no funcionan con estos)
        if (strpos($host, 'localhost') !== false || filter_var($host, FILTER_VALIDATE_IP)) {
            return '';
        }

        // Para dominios como app.midominio.com -> .midominio.com
        if (substr_count($host, '.') > 1) {
            $parts = explode('.', $host);
            return '.' . $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
        }

        return '.' . $host;
    }

    private function isHttps()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? null) === 443;
    }

    /**
     * Genera y almacena un nuevo token JWT
     */
    public function generateAndStoreToken($id_user, $email, $id_company): string
    {
        $this->startSecureSession();

        $payload = [
            "data" => $id_user,
            "email" => $email,
            "id_company" => $id_company,
            "iat" => time(),
            "exp" => time() + (8 * 3600) // 8 horas
        ];

        $token = JWT::encode($payload, $this->jwtSecret, $this->jwtAlgorithm);

        $_SESSION['token'] = $token;
        $this->saveJWTToDatabase($id_user, $token);

        return $token;
    }

    public function validationToken($info)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $sql = "SELECT * FROM users WHERE id_user = :id_user";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_user' => $info->data]);
            $user = $stmt->fetch($connection::FETCH_ASSOC);

            if (!$user) {
                $stmt = $connection->prepare("SELECT * FROM admins WHERE id_admin = :id_admin");
                $stmt->execute(['id_admin' => $info->data]);
                $user = $stmt->fetch($connection::FETCH_ASSOC);
            }

            return $user;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }



    /**
     * Destruye la sesión de manera segura
     */
    public function destroySession()
    {
        $this->startSecureSession();

        // Limpiar token de base de datos si hay usuario en sesión
        if (!empty($_SESSION['idUser'])) {
            $this->saveJWTToDatabase($_SESSION['idUser'], null);
        }

        // Destruir sesión PHP
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    private function saveJWTToDatabase($id_user, $token)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $sql = "UPDATE users SET token_pass = :token, session_active = :active WHERE id_user = :id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'token' => $token,
                'active' => $token ? 1 : 0,
                'id' => $id_user
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to save JWT to DB', ['error' => $e->getMessage()]);
        }
    }
}
