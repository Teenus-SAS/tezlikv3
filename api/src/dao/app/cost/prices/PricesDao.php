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
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.cost_workforce, pc.cost_materials, pc.cost_indirect_cost, pc.profitability, pc.commission_sale, pc.sale_price, IF(cl.flag_family = 2, (SELECT IFNULL(SUM(units_sold), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(units_sold), 0) FROM expenses_distribution WHERE id_company = p.id_company)) AS units_sold,
                                         IF(cl.flag_family = 2, (SELECT IFNULL(SUM(turnover), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(turnover),0) FROM expenses_distribution WHERE id_company = p.id_company)) AS turnover, ed.assignable_expense,
                                         IFNULL(er.expense_recover, 0) AS expense_recover, IFNULL((SELECT SUM(cost) FROM services WHERE id_product = p.id_product), 0) AS services, p.img, pc.price, pc.price_usd, (SELECT cost_price FROM plans_access WHERE id_plan = cl.plan) AS details_product
                                  FROM products p
                                    LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                    LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                    INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                    INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                                  WHERE p.id_company = :id_company AND p.active = 1
                                  ORDER BY `p`.`reference` ASC");

    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $prices = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("prices", array('prices' => $prices));
    return $prices;
  }

  public function findPriceByProduct($id_product, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_product, pc.profitability, pc.price, pc.sale_price
                                  FROM products p
                                  INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                  WHERE p.id_product = :id_product AND p.id_company = :id_company");
    $stmt->execute([
      'id_product' => $id_product,
      'id_company' => $id_company
    ]);
    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $price = $stmt->fetch($connection::FETCH_ASSOC);
    return $price;
  }

  public function findCustomPriceByPriceList($id_price_list)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM custom_prices WHERE id_price_list = :id_price_list");
    $stmt->execute(['id_price_list' => $id_price_list]);
    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $price = $stmt->fetch($connection::FETCH_ASSOC);
    return $price;
  }
}
