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

        $stmt = $connection->prepare("SELECT o.id_order, o.num_order, o.date_order, o.original_quantity, o.quantity, o.accumulated_quantity, p.product, c.client
                                      FROM orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN clients c ON c.id_client = o.id_client
                                      WHERE o.status_order = 0 AND o.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Pedidos", array('Pedidos' => $orders));
        return $orders;
    }

    // Obtener informacion pedido
    public function findOrdersByCompany($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.num_order, o.date_order, o.original_quantity, o.quantity, o.accumulated_quantity, p.product, c.client
                                      FROM orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN clients c ON c.id_client = o.id_client
                                      WHERE o.id_order = :id_order AND o.id_company = :id_company");
        $stmt->execute([
            'id_order' => $dataOrder['order'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("Pedido", array('Pedido' => $order));
        return $order;
    }


    public function findOrder($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM orders 
                                      WHERE num_order = :num_order AND id_company = :id_company");
        $stmt->execute([
            'num_order' => $dataOrder['order'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("Pedidos", array('Pedidos' => $orders));
        return $orders;
    }

    public function insertOrderByCompany($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $date = str_replace('/', '-', $dataOrder['dateOrder']);
        $dateOrder = date('Y-m-d', strtotime($date));

        try {
            $stmt = $connection->prepare("INSERT INTO orders (num_order, date_order, id_company, id_product, id_client, original_quantity, quantity) 
                                          VALUES (:num_order, :date_order, :id_company, :id_product, :id_client, :original_quantity, :quantity)");
            $stmt->execute([
                'num_order' => $dataOrder['order'],
                'date_order' => $dateOrder,
                'id_company' => $id_company,
                'id_product' => $dataOrder['idProduct'],
                'id_client' => $dataOrder['idClient'],
                'original_quantity' => $dataOrder['originalQuantity'],
                'quantity' => $dataOrder['quantity'],
                // 'accumulated_quantity' => $dataOrder['accumulatedQuantity']
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

        $date = str_replace('/', '-', $dataOrder['dateOrder']);
        $dateOrder = date('Y-m-d', strtotime($date));

        try {
            $stmt = $connection->prepare("UPDATE orders SET num_order = :num_order, date_order = :date_order, id_product = :id_product, id_client = :id_client, 
                                          original_quantity = :original_quantity, quantity = :quantity
                                          WHERE id_order = :id_order");
            $stmt->execute([
                'num_order' => $dataOrder['order'],
                'date_order' => $dateOrder,
                'id_product' => $dataOrder['idProduct'],
                'id_client' => $dataOrder['idClient'],
                'original_quantity' => $dataOrder['originalQuantity'],
                'quantity' => $dataOrder['quantity'],
                // 'accumulated_quantity' => $dataOrder['accumulatedQuantity'],
                'id_order' => $dataOrder['idOrder']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeStatus($orders)
    {
        $connection = Connection::getInstance()->getConnection();

        $orders = json_encode($orders);
        $data = str_replace('"', '', $orders);
        $data = substr($data, 1, -1);

        $stmt = $connection->prepare("UPDATE orders SET status_order = 1
                                      WHERE id_order IN(SELECT id_order FROM orders WHERE num_order NOT IN({$data}))");
        $stmt->execute();
    }
}
