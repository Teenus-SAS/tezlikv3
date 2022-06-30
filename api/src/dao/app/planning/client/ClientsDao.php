<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ClientsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findClient($dataClient, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM clients WHERE client = :client AND id_company = :id_company");
        $stmt->execute([
            'client' => ucfirst(strtolower(trim($dataClient['client']))),
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $client = $stmt->fetch($connection::FETCH_ASSOC);
        // $this->logger->notice("Cliente", array('Clientes' => $client));
        return $client;
    }

    public function insertClient($dataClient, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO clients (client, id_company) VALUES (:client, :id_company)");
            $stmt->execute([
                'client' => ucfirst(strtolower(trim($dataClient['client']))),
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            // Obtener id cliente
            $client = $this->findClient($dataClient, $id_company);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $client = array('info' => true, 'message' => $message);
        }
        return $client;
    }

    public function updateClient($dataClient)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE clients SET client = :client WHERE id_client = :id_client");
            $stmt->execute([
                'client' => ucfirst(strtolower(trim($dataClient['client']))),
                'id_client' => $dataClient['idClient']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteClient($id_client)
    {
        $connection = Connection::getInstance()->getconnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM clients WHERE id_client = :id_client");
            $stmt->execute(['id_client' => $id_client]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM clients WHERE id_client = :id_client");
                $stmt->execute(['id_client' => $id_client]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
