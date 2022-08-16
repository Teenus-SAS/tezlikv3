<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class OrderTypesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findOrderTypes($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM order_types WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orderTypes = $stmt->fetch($connection::FETCH_ASSOC);
        return $orderTypes;
    }

    public function insertOrderTypes($dataOrderTypes, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO order_types (id_order, order_type) VALUES (:id_order, :order_type)");
            $stmt->execute([
                'ean' => $dataOrderTypes['ean'],
                'id_order' => $dataOrderTypes['id_order'],
                'order_type' => ucfirst(strtolower(trim($dataOrderTypes['OrderTypes'])))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateOrderTypes($dataOrderTypes)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE order_types SET id_order = :id_order, order_type = :order_type WHERE id_order_type = :id_order_type");
            $stmt->execute([
                'id_order' => $dataOrderTypes['id_order'],
                'order_type' => ucfirst(strtolower(trim($dataOrderTypes['order_type']))),
                'id_order_type' => $dataOrderTypes['idOrderType']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteOrderTypes($id_order_type)
    {
        $connection = Connection::getInstance()->getconnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM order_types WHERE id_order_type = :id_order_type");
            $stmt->execute(['id_order_type' => $id_order_type]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM order_types WHERE id_order_type = :id_order_type");
                $stmt->execute(['id_order_type' => $id_order_type]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
