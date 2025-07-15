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
    $sql = "SELECT p.id_product, p.reference, p.product, p.composite, p.img,
            -- Costeo Total
              IFNULL(pc.cost_materials, 0) AS cost_materials, 
              IFNULL(pc.cost_workforce, 0) AS cost_workforce, 
              IFNULL(pc.cost_indirect_cost, 0) AS cost_indirect_cost, 
              IFNULL((SELECT SUM(cost) FROM services WHERE id_product = p.id_product), 0) AS services, 
              IFNULL(pc.commission_sale, 0) AS commission_sale, 
              IFNULL(pc.profitability, 0) AS profitability, 
            -- Precios Producto
              IFNULL(pc.price, 0) AS price, 
              IFNULL(pc.sale_price, 0) AS sale_price, 
              IFNULL(pc.price_usd, 0) AS price_usd, 
              IFNULL(pc.sale_price_usd, 0) AS sale_price_usd, 
              IFNULL(pc.price_eur, 0) AS price_eur, 
              IFNULL(pc.sale_price_eur, 0) AS sale_price_eur, 
            -- Ventas
              IF(cl.flag_family = 2, (SELECT IFNULL(SUM(units_sold), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(units_sold), 0) FROM expenses_distribution WHERE id_product = p.id_product)) AS units_sold,
              IF(cl.flag_family = 2, (SELECT IFNULL(SUM(turnover), 0) FROM families WHERE id_company = p.id_company), (SELECT IFNULL(SUM(turnover), 0) FROM expenses_distribution WHERE id_product = p.id_product)) AS turnover, 
              IF(cl.flag_family = 2, IFNULL(f.assignable_expense, 0), IFNULL(ed.assignable_expense, 0)) AS assignable_expense, 
              IFNULL(er.expense_recover, 0) AS expense_recover,
              IFNULL(eda.units_sold, 0) AS units_sold_anual,
              IFNULL(eda.turnover, 0) AS turnover_anual
            FROM products p
              LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
              LEFT JOIN expenses_recover er ON er.id_product = p.id_product
              LEFT JOIN expenses_distribution_anual eda ON eda.id_product = p.id_product
              LEFT JOIN families f ON f.id_family = p.id_family
              INNER JOIN products_costs pc ON pc.id_product = p.id_product
              INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
            WHERE p.id_company = :id_company AND p.active = 1
            ORDER BY `p`.`reference` ASC";
    $stmt = $connection->prepare($sql);

    $stmt->execute(['id_company' => $id_company]);



    $prices = $stmt->fetchAll($connection::FETCH_ASSOC);

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


    $price = $stmt->fetch($connection::FETCH_ASSOC);
    return $price;
  }

  public function findCustomPriceByPriceList($id_price_list)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM custom_prices WHERE id_price_list = :id_price_list");
    $stmt->execute(['id_price_list' => $id_price_list]);


    $price = $stmt->fetch($connection::FETCH_ASSOC);
    return $price;
  }
}
