<?php

namespace tezlikv3\dao;

use DateTime;
use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PucsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    //Obtener todos las cuentas PUC
    public function findAllCounts()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM puc ORDER BY id_puc ASC");
        $stmt->execute();
        $puc = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("puc", array('puc' => $puc));
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        return $puc;
    }


    //Ingresar PUC
    public function insertCountsPUC($dataPuc)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_puc FROM puc WHERE number_count = :number_count AND count = :count");
        $stmt->execute([
            'number_count' => trim($dataPuc['accountNumber']),
            'count' => ucfirst(strtolower(trim($dataPuc['account'])))
        ]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            return 1;
        } else {
            $stmt = $connection->prepare("INSERT INTO puc (number_count, count) VALUES (:number_count, :count)");
            $stmt->execute([
                'number_count' => trim($dataPuc['accountNumber']),
                'count' => ucfirst(strtolower(trim($dataPuc['account'])))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }


    //Actualizar PUC
    public function updateCountsPUC($dataPuc)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE puc SET number_count = :number_count, count = :count
                                          WHERE id_puc = :id_puc");
            $stmt->execute([
                'id_puc' => trim($dataPuc['id_puc']),
                'number_count' => trim($dataPuc['accountNumber']),
                'count' => ucfirst(strtolower(trim($dataPuc['account'])))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
