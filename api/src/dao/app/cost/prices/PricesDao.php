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
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.profitability, pc.commission_sale, ed.units_sold, ed.turnover, p.img, pc.price
                                  FROM products p
                                  INNER JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                  INNER JOIN products_costs pc ON pc.id_product = p.id_product 
                                  WHERE p.id_company = :id_company 
                                  ORDER BY `pc`.`profitability` DESC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $prices = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("prices", array('prices' => $prices));
    return $prices;
  }
}
