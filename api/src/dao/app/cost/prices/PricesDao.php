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
                                         pc.price, (SELECT cost_price FROM plans_access WHERE id_plan = cl.plan) AS details_product
                                  FROM products p
                                    LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                    INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                    INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                                  WHERE p.id_company = :id_company AND p.active = 1
                                  ORDER BY `pc`.`price` DESC, `pc`.`profitability` DESC");

    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $prices = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("prices", array('prices' => $prices));
    return $prices;
  }

  public function findPriceByProduct($id_product, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_product, CAST(pc.price AS UNSIGNED) AS price
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
}
