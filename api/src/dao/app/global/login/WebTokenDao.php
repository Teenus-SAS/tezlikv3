<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Firebase\JWT\JWT;

class WebTokenDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
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
