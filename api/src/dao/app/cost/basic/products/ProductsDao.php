<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllProductsByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $sql = "SELECT p.id_product, p.reference, p.product, pc.sale_price, pc.profitability, pc.commission_sale, pc.price, p.img, p.id_family, p.composite, 
                  IFNULL((SELECT id_composite_product FROM composite_products WHERE id_product = p.id_product LIMIT 1), 0) AS composite_product, p.active
            FROM products p
              INNER JOIN products_costs pc ON p.id_product = pc.id_product
            WHERE p.id_company = :id_company
            ORDER BY `p`.`product`, `p`.`reference` ASC";
    $stmt = $connection->prepare($sql);
    $stmt->execute(['id_company' => $id_company]);



    $products = $stmt->fetchAll($connection::FETCH_ASSOC);

    return $products;
  }

  /* Insertar producto */
  public function insertProductByCompany($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {

      $sql = "INSERT INTO products(id_company, reference, product, active) 
              VALUES(:id_company, :reference, :product, :active)";

      $stmt = $connection->prepare($sql);
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'active' => $dataProduct['active'],
        'id_company' => $id_company,
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar producto */
  public function updateProductByCompany($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $sql = "UPDATE products SET reference = :reference, product = :product, active = :active
              WHERE id_product = :id_product AND id_company = :id_company";
      $stmt = $connection->prepare($sql);
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'id_product' => $dataProduct['idProduct'],
        'active' => $dataProduct['active'],
        'id_company' => $id_company
      ]);
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteProduct($id_product)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("SELECT * FROM products WHERE id_product = :id_product");
      $stmt->execute(['id_product' => $id_product]);
      $rows = $stmt->rowCount();

      if ($rows > 0) {
        $stmt = $connection->prepare("DELETE FROM products WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();
      if ($e->getCode() == 23000)
        $message = 'No es posible eliminar, el producto esta asociado a cotización';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
