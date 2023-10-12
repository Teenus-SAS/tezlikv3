<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ContractDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findContract()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM contracts");
        $stmt->execute();

        $contract = $stmt->fetch($connection::FETCH_ASSOC);
        return $contract;
    }

    public function insertContract($dataContract)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO contracts (content) VALUES (:content)");
            $stmt->execute([
                'content' => $dataContract['content']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateContract($dataContract)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE contracts SET content = :content WHERE id_contract = :id_contract");
            $stmt->execute([
                'id_contract' => $dataContract['idContract'],
                'content' => $dataContract['content']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
