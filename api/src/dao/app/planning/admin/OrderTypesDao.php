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

    public function findAllOrderTypes()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM order_types");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $orderTypes = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $orderTypes;
    }

    public function findOrderType($dataOrderTypes)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM order_types WHERE order_type = :order_type");
        $stmt->execute([
            'order_type' => ucfirst(strtolower(trim($dataOrderTypes['orderType'])))
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $orderType = $stmt->fetch($connection::FETCH_ASSOC);
        return $orderType;
    }

    public function insertOrderTypes($dataOrderTypes)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO order_types (order_type) VALUES (:order_type)");
            $stmt->execute([
                'order_type' => ucfirst(strtolower(trim($dataOrderTypes['orderType'])))
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
            $stmt = $connection->prepare("UPDATE order_types SET order_type = :order_type WHERE id_order_type = :id_order_type");
            $stmt->execute([
                'order_type' => ucfirst(strtolower(trim($dataOrderTypes['orderType']))),
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
