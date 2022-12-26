<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PricesDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllPricesByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.profitability, pc.commission_sale, IFNULL(ed.units_sold, 0) AS units_sold, IFNULL(ed.turnover, 0) AS turnover, p.img, 
                                         CAST((((IFNULL(pc.cost_workforce + pc.cost_materials, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product)) / (1 - IFNULL(er.expense_recover, 0) / 100)) 
                                         / (1 - (IFNULL(pc.profitability, 0) /100))) / (1 - (IFNULL(pc.commission_sale, 0) / 100)) AS UNSIGNED) AS price, 
                                         CAST(IFNULL(pc.price / pc.profitability, 0) AS UNSIGNED) AS cost, 
                                         (SELECT cost_price FROM plans_access WHERE id_plan = cl.plan) AS details_product
                                  FROM products p
                                    LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                    INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                    LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                    INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                                  WHERE p.id_company = :id_company AND p.active = 1
                                  ORDER BY `p`.`product` ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $prices = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("prices", array('prices' => $prices));
    return $prices;
  }

  public function findPriceByProduct($id_product, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT CAST((((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost + (SELECT SUM(cost) FROM services WHERE id_product = p.id_product))/(1 - IFNULL(er.expense_recover, 0) / 100)) 
                                         / (1 - (pc.profitability /100))) / (1 - (pc.commission_sale / 100)) AS UNSIGNED) AS cost
                                  FROM products p
                                  LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                  INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                  LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                  WHERE p.id_product = :id_product AND p.id_company = :id_company");
    $stmt->execute([
      'id_product' => $id_product,
      'id_company' => $id_company
    ]);
    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $price = $stmt->fetch($connection::FETCH_ASSOC);
    return $price;
  }
}
