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

    // Calcular precio por producto
    public function calcPrice($idProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) {
                $sql = "SELECT
                        (((IFNULL(pc.cost_workforce + pc.cost_materials, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product)) + IF(cl.flag_family = 2, IFNULL(f.assignable_expense, 0), IFNULL(ed.assignable_expense, 0))) 
                        / (1 - (IFNULL(pc.profitability, 0) /100))) / (1 - (IFNULL(pc.commission_sale, 0) / 100)) AS totalPrice, pc.sale_price
                        FROM products p
                            LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                            LEFT JOIN services s ON s.id_product = p.id_product
                            LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                            LEFT JOIN families f ON f.id_family = p.id_family
                            INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                        WHERE p.id_product = :id_product";
                $stmt = $connection->prepare($sql);
                $stmt->execute(['id_product' => $idProduct]);
            } else {
                $sql = "SELECT
                        (((IFNULL(pc.cost_workforce + pc.cost_materials, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product)) 
                        / (1 - IFNULL(er.expense_recover, 0) / 100)) / (1 - (IFNULL(pc.profitability, 0) /100))) / (1 - (IFNULL(pc.commission_sale, 0) / 100)) AS totalPrice, pc.sale_price
                        FROM products p
                            LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                            LEFT JOIN services s ON s.id_product = p.id_product
                            LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                        WHERE p.id_product = :id_product";
                $stmt = $connection->prepare($sql);
                $stmt->execute(['id_product' => $idProduct]);
            }
            $dataPrice = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $dataPrice = array('info' => true, 'message' => $message);
        }

        return $dataPrice;
    }

    // Calcular todos los precio por empresa
    public function calcPriceByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) {
                // Caso manual o sin recuperación
                $sql = "UPDATE products_costs pc
                    INNER JOIN products p ON pc.id_product = p.id_product
                    INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                    LEFT JOIN services s ON s.id_product = p.id_product
                    LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                    LEFT JOIN families f ON f.id_family = p.id_family
                    SET pc.price = ROUND(
                        (
                            (
                                IFNULL(pc.cost_workforce + pc.cost_materials, 0)
                                + IFNULL(pc.cost_indirect_cost, 0)
                                + (
                                    SELECT IFNULL(SUM(s2.cost), 0)
                                    FROM services s2
                                    WHERE s2.id_product = p.id_product
                                )
                                + IF(cl.flag_family = 2, IFNULL(f.assignable_expense, 0), IFNULL(ed.assignable_expense, 0))
                            ) / (1 - (IFNULL(pc.profitability, 0) / 100))
                        ) / (1 - (IFNULL(pc.commission_sale, 0) / 100)), 2
                    )
                    WHERE p.id_company = :id_company";
            } else {
                // Caso automático con recuperación
                $sql = "UPDATE products_costs pc
                    INNER JOIN products p ON pc.id_product = p.id_product
                    LEFT JOIN services s ON s.id_product = p.id_product
                    LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                    SET pc.price = ROUND(
                        (
                            (
                                (
                                    IFNULL(pc.cost_workforce + pc.cost_materials, 0)
                                    + IFNULL(pc.cost_indirect_cost, 0)
                                    + (
                                        SELECT IFNULL(SUM(s2.cost), 0)
                                        FROM services s2
                                        WHERE s2.id_product = p.id_product
                                    )
                                ) / (1 - IFNULL(er.expense_recover, 0) / 100)
                            ) / (1 - (IFNULL(pc.profitability, 0) / 100))
                        ) / (1 - (IFNULL(pc.commission_sale, 0) / 100)), 2
                    )
                    WHERE p.id_company = :id_company";
            }

            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_company' => $id_company]);

            return ['success' => true, 'message' => 'Precios actualizados correctamente.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Calcular todos los precio por empresa
    public function calcPriceByProduct($id_company, $product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) {
                // Caso manual o sin recuperación
                $sql = "UPDATE products_costs pc
                        INNER JOIN products p ON pc.id_product = p.id_product
                        INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                        LEFT JOIN services s ON s.id_product = p.id_product
                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                        LEFT JOIN families f ON f.id_family = p.id_family
                        SET pc.price = ROUND(
                            (
                                (
                                    IFNULL(pc.cost_workforce + pc.cost_materials, 0)
                                    + IFNULL(pc.cost_indirect_cost, 0)
                                    + (
                                        SELECT IFNULL(SUM(s2.cost), 0)
                                        FROM services s2
                                        WHERE s2.id_product = p.id_product
                                    )
                                    + IF(cl.flag_family = 2, IFNULL(f.assignable_expense, 0), IFNULL(ed.assignable_expense, 0))
                                ) / (1 - (IFNULL(pc.profitability, 0) / 100))
                            ) / (1 - (IFNULL(pc.commission_sale, 0) / 100)), 2
                        )
                        WHERE p.id_company = :id_company AND p.id_product = :id_product";
            } else {
                // Caso automático con recuperación
                $sql = "UPDATE products_costs pc
                    INNER JOIN products p ON pc.id_product = p.id_product
                    LEFT JOIN services s ON s.id_product = p.id_product
                    LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                    SET pc.price = ROUND(
                        (
                            (
                                (
                                    IFNULL(pc.cost_workforce + pc.cost_materials, 0)
                                    + IFNULL(pc.cost_indirect_cost, 0)
                                    + (
                                        SELECT IFNULL(SUM(s2.cost), 0)
                                        FROM services s2
                                        WHERE s2.id_product = p.id_product
                                    )
                                ) / (1 - IFNULL(er.expense_recover, 0) / 100)
                            ) / (1 - (IFNULL(pc.profitability, 0) / 100))
                        ) / (1 - (IFNULL(pc.commission_sale, 0) / 100)), 2
                    )
                    WHERE p.id_company = :id_company AND p.id_product = :id_product";
            }

            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_company' => $id_company, 'id_product' => $product[0]['id_product']]);

            return ['success' => true, 'message' => 'Precios actualizados correctamente.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
