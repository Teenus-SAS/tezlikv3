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

        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, hp.id_historic, hp.month, hp.year, hp.price, hp.sale_price, hp.profitability, hp.min_profitability
                                      FROM tezlikso_tezlikproduccion.products p
                                        JOIN tezlikhistproduccion.historical_products hp ON hp.id_product = p.id_product
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
