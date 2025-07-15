<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralOrdersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Obtener informacion pedido
    public function findOrdersByCompany($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.num_order, o.date_order, o.original_quantity, o.quantity, o.accumulated_quantity, p.product, c.client
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                      WHERE o.id_order = :id_order AND o.id_company = :id_company");
        $stmt->execute([
            'id_order' => $dataOrder['order'],
            'id_company' => $id_company
        ]);


        $order = $stmt->fetch($connection::FETCH_ASSOC);

        return $order;
    }

    public function findAllOrdersConcat($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT CONCAT(num_order, '-' , id_product) AS concate FROM plan_orders WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);


        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $orders;
    }

    public function changeStatus($num_order, $id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_orders SET status_order = 0 WHERE num_order = :num_order AND id_product = :id_product");
            $stmt->execute([
                'num_order' => $num_order,
                'id_product' => $id_product,
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
