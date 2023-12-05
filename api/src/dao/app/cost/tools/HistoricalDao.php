<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HistoricalDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllHistoricalByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $month = date('m');
        $year = date('Y');

        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, hp.id_historic, hp.month, hp.year, hp.price, hp.sale_price, hp.profitability, hp.min_profitability, pc.cost_workforce AS actual_cost_workforce, pc.cost_materials AS actual_cost_materials, pc.cost_indirect_cost AS actual_cost_indirect_cost, pc.profitability AS actual_profitability, pc.commission_sale AS actual_commission_sale, pc.sale_price AS actual_sale_price, pc.price AS actual_price, IF(cl.flag_family = 2, (SELECT IFNULL(SUM(units_sold), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(units_sold), 0) FROM expenses_distribution WHERE id_company = p.id_company)) AS actual_units_sold,
                                         IF(cl.flag_family = 2, (SELECT IFNULL(SUM(turnover), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(turnover),0) FROM expenses_distribution WHERE id_company = p.id_company)) AS actual_turnover, IF(cl.flag_family = 2, IFNULL(f.assignable_expense, 0), IFNULL(ed.assignable_expense, 0)) AS actual_assignable_expense,
                                         IFNULL(er.expense_recover, 0) AS expense_recover, IFNULL((SELECT SUM(cost) FROM services WHERE id_product = p.id_product), 0) AS actual_services
                                      FROM products p
                                        JOIN tezlikso_histproduccion.historical_products hp ON hp.id_product = p.id_product
                                        LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                        LEFT JOIN companies_licenses cl ON cl.id_company = p.id_company
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                        LEFT JOIN families f ON f.id_family = p.id_family
                                      WHERE p.id_company = :id_company AND hp.month = :month AND hp.year = :year");
        $stmt->execute([
            'id_company' => $id_company,
            'month' => $month,
            'year' => $year
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    // public function findVariableCostByProduct($id_product, $id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();
    //     $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, hp.id_historic, hp.month, hp.year, hp.price, hp.sale_price, hp.profitability, hp.min_profitability, hp.commision_sale, hp.cost_material, hp.cost_workforce, hp.cost_indirect, hp.external_services, hp.units_sold, hp.turnover, hp.assignable_expense
    //   FROM tezlikso_tezlikproduccion.products p
    //     JOIN tezlikhistproduccion.historical_products hp ON hp.id_product = p.id_product
    //   WHERE p.id_company = :id_company");
    //     $stmt->execute([
    //         'id_product' => $id_product,
    //         'id_company' => $id_company
    //     ]);
    //     $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    //     $variablesCosts = $stmt->fetch($connection::FETCH_ASSOC);
    //     return $variablesCosts;
    // }
}
