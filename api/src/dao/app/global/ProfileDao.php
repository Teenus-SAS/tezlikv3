<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProfileDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function updateProfile($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname
                                          WHERE id_user = :id_user");
            $stmt->execute([
                'id_user' => $dataUser['idUser'],
                'firstname' => $dataUser['nameUser'],
                'lastname' => $dataUser['lastnameUser']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function avatarUser($id_user, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/users/' . $id_company;
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = $_FILES['avatar']['name'];
        $tmp_name   = $_FILES['avatar']['tmp_name'];
        $size       = $_FILES['avatar']['size'];
        $type       = $_FILES['avatar']['type'];
        $error      = $_FILES['avatar']['error'];

        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/assets/images/users/' . $id_company;
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE users SET avatar = :avatar WHERE id_user = :id_user AND id_company = :id_company";
            $query = $connection->prepare($sql);
            $query->execute([
                'avatar' => $targetFilePath,
                'id_user' => $id_user,
                'id_company' => $id_company
            ]);

            $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/users/' . $id_company;
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
        }
    }
}