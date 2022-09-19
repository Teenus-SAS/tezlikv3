<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ConsolidatedDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findConsolidated($id_company)
    {
        $connection = Connection::getInstance()->getconnection();
        /*
        $stmt = $connection->prepare("SELECT o.num_order, p.reference, p.quantity, IF(o.id_order_type = 1, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) AS cadenas, 
                                            IF(o.id_order_type = 2, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) AS venta_directa, IF(o.id_order_type = 3, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) AS exportadas,
                                            (IF(o.id_order_type = 1, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) + 
                                            IF(o.id_order_type = 2, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) + 
                                            IF(o.id_order_type = 3, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0)) AS total_orders, 
                                            ROUND(IFNULL((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12, 0)) AS average_month, 
                                            ROUND(IFNULL(p.quantity/(((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12)/4/7), 0)) AS inventory_days,
                                            0 AS week_minimum_stock, 0 AS produce_ajusted
                                      FROM products p
                                      INNER JOIN plan_orders o ON o.id_product = p.id_product
                                      LEFT JOIN products_price_history pph ON pph.id_product = p.id_product        
                                      WHERE o.id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $consolidated = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $consolidated;*/
    }

    public function calcConsolidated($week, $id_company)
    {
        $connection = Connection::getInstance()->getconnection();

        $stmt = $connection->prepare("SELECT o.num_order, p.reference, p.quantity, IF(o.id_order_type = 1, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) AS cadenas, 
                                             IF(o.id_order_type = 2, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) AS venta_directa, IF(o.id_order_type = 3, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) AS exportadas,
                                             (IF(o.id_order_type = 1, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) + 
                                              IF(o.id_order_type = 2, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) + 
                                              IF(o.id_order_type = 3, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0)) AS total_orders, 
                                             ROUND(IFNULL((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12, 0)) AS average_month, 
                                             ROUND(IFNULL(p.quantity/(((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12)/4/7), 0)) AS inventory_days,
                                             ROUND(IFNULL(((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12)/4*:week,0)) AS week_minimum_stock,
                                             ROUND(IFNULL(((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12)/4*:week + (IF(o.id_order_type = 1, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) + 
                                              IF(o.id_order_type = 2, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) + 
                                              IF(o.id_order_type = 3, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0)) - p.quantity, 0)) AS produce_ajusted
                                      FROM products p
                                      INNER JOIN plan_orders o ON o.id_product = p.id_product
                                      LEFT JOIN products_price_history pph ON pph.id_product = p.id_product        
                                      WHERE o.id_company = :id_company;");
        $stmt->execute([
            'week' => $week,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $consolidated = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $consolidated;
    }
}
