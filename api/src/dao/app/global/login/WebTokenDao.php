<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class WebTokenDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function getToken()
    {
        $connection = Connection::getInstance()->getConnection();

        // $headers = apache_request_headers();
        if (!isset($_SESSION))
            session_start();
        if (!isset($_SESSION['token'])) {
            return 1;
        }

        // $authorization = $headers['Authorization'];
        // $authorizationArray = explode(' ', $authorization);

        // $token = $authorizationArray['1'];
        $token = $_SESSION['token'];
        try {
            $decoded = JWT::decode($token, new Key($_ENV['jwt_key'], 'HS256'));
        } catch (\Exception $e) {
            $stmt = $connection->prepare("UPDATE users SET session_active = :session_active WHERE id_user = :id_user");
            $stmt->execute([
                'session_active' => 0,
                'id_user' => $_SESSION['idUser']
            ]);
            session_destroy();

            return ['info' => $e->getMessage()];
        }
        return $decoded;
    }

    public function validationToken($info)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("SELECT * FROM users WHERE id_user = :id_user");
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

    public function setJSONWebToken($id_user, $email, $id_company)
    {
        try {
            $secretPass = "tu_clave_secreta";

            $payload = array(
                "id_user" => $id_user,
                "email" => $email,
                "id_company" => $id_company,
                "iat" => time(),
                "exp" => time() + 3600
            );
            $jwt = JWT::encode($payload, $secretPass, 'HS256');

            return $jwt;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function saveJWTUser($jwt, $id_user)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE users SET token_pass = :token_pass WHERE id_user = :id_user");
            $stmt->execute([
                'id_user' => $id_user,
                'token_pass' => $jwt,
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
