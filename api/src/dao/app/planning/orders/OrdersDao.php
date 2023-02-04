<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class OrdersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllOrdersByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, o.id_product, o.id_order_type, o.num_order, o.date_order, o.original_quantity, o.quantity, p.product, c.client, ot.order_type, o.min_date, o.max_date, o.delivery_date
                                      FROM plan_orders o
                                        INNER JOIN plan_orders_types ot ON ot.id_order_type = o.id_order_type
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                      WHERE o.status_order = 0 AND o.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findOrder($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_orders WHERE num_order = :num_order AND id_company = :id_company");
        $stmt->execute([
            'num_order' => trim($dataOrder['order']),
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $orders = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function insertOrderByCompany($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO plan_orders (num_order, date_order, min_date, max_date, id_company, id_product, id_client, id_order_type, original_quantity, quantity, status_order) 
                                          VALUES (:num_order, :date_order, :min_date, :max_date, :id_company, :id_product, :id_client, :id_order_type, :original_quantity, :quantity, 1)");
            $stmt->execute([
                'num_order' => trim($dataOrder['order']),
                'date_order' => trim($dataOrder['dateOrder']),
                'min_date' => trim($dataOrder['minDate']),
                'max_date' => trim($dataOrder['maxDate']),
                'id_company' => $id_company,
                'id_product' => $dataOrder['idProduct'],
                'id_client' => $dataOrder['idClient'],
                'id_order_type' => $dataOrder['idOrderType'],
                'original_quantity' => trim($dataOrder['originalQuantity']),
                'quantity' => trim($dataOrder['quantity'])
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Pedido duplicado. Ingrese una nuevo pedido';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateOrder($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_orders SET num_order = :num_order, date_order = :date_order, min_date = :min_date, max_date = :max_date, id_product = :id_product,
                                                 id_client = :id_client, id_order_type = :id_order_type, original_quantity = :original_quantity, quantity = :quantity, status_order = 0
                                          WHERE id_order = :id_order");
            $stmt->execute([
                'num_order' => trim($dataOrder['order']),
                'date_order' => trim($dataOrder['dateOrder']),
                'min_date' => trim($dataOrder['minDate']),
                'max_date' => trim($dataOrder['maxDate']),
                'id_product' => $dataOrder['idProduct'],
                'id_client' => $dataOrder['idClient'],
                'id_order_type' => $dataOrder['idOrderType'],
                'original_quantity' => trim($dataOrder['originalQuantity']),
                'quantity' => trim($dataOrder['quantity']),
                'id_order' => $dataOrder['idOrder']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteOrder($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM plan_orders WHERE id_order = :id_order");
            $stmt->execute(['id_order' => $id_order]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM plan_orders WHERE id_order = :id_order");
                $stmt->execute(['id_order' => $id_order]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
