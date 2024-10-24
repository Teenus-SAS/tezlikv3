<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PriceProductDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Calcular precio del producto
    public function calcPrice($idProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) {
                $stmt = $connection->prepare("SELECT
                                                (((IFNULL(pc.cost_workforce + pc.cost_materials, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product)) + IF(cl.flag_family = 2, IFNULL(f.assignable_expense, 0), IFNULL(ed.assignable_expense, 0))) 
                                                / (1 - (IFNULL(pc.profitability, 0) /100))) / (1 - (IFNULL(pc.commission_sale, 0) / 100)) AS totalPrice, pc.sale_price
                                                FROM products p
                                                    LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                                    LEFT JOIN services s ON s.id_product = p.id_product
                                                    LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                                    LEFT JOIN families f ON f.id_family = p.id_family
                                                    INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                                                WHERE p.id_product = :id_product");
                $stmt->execute(['id_product' => $idProduct]);
            } else {
                $stmt = $connection->prepare("SELECT
                                                (((IFNULL(pc.cost_workforce + pc.cost_materials, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product)) 
                                                / (1 - IFNULL(er.expense_recover, 0) / 100)) / (1 - (IFNULL(pc.profitability, 0) /100))) / (1 - (IFNULL(pc.commission_sale, 0) / 100)) AS totalPrice, pc.sale_price
                                                FROM products p
                                                    LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                                    LEFT JOIN services s ON s.id_product = p.id_product
                                                    LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                                WHERE p.id_product = :id_product");
                $stmt->execute(['id_product' => $idProduct]);
            }
            $dataPrice = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $dataPrice = array('info' => true, 'message' => $message);
        }

        return $dataPrice;
    }
}
