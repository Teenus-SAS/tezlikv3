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

  public function findProductCost($id_product, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT p.img, IFNULL(pc.price, 0) AS price
                                  FROM products p
                                  LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                  WHERE p.id_product = :id_product AND p.id_company = :id_company");
    $stmt->execute([
      'id_product' => $id_product,
      'id_company' => $id_company
    ]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $product = $stmt->fetch($connection::FETCH_ASSOC);
    return $product;
  }

  public function findAllProductsByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, ROUND(IFNULL(pc.profitability, 0), 2) AS profitability, ROUND(IFNULL(pc.commission_sale, 0), 2) AS commission_sale, pc.price, p.img 
                                  FROM products p
                                    LEFT JOIN products_costs pc ON p.id_product = pc.id_product
                                  WHERE p.id_company = :id_company AND p.active = 1 ORDER BY `p`.`product`, `p`.`reference` ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $products = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("products", array('products' => $products));
    return $products;
  }

  public function findAllProductsByCRM($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, IFNULL(pc.price, 0) AS price, p.img 
                                  FROM products p
                                    LEFT JOIN products_costs pc ON p.id_product = pc.id_product
                                  WHERE p.id_company = :id_company AND p.active = 1 ORDER BY `p`.`product`, `p`.`reference` ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $products = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("products", array('products' => $products));
    return $products;
  }

  public function findAllInactivesProducts($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, IFNULL(pc.profitability, 0) AS profitability, IFNULL(pc.commission_sale, 0) AS commission_sale, pc.price, p.img 
                                  FROM products p
                                    LEFT JOIN products_costs pc ON p.id_product = pc.id_product
                                  WHERE p.id_company = :id_company AND p.active = 0");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $products = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("products", array('products' => $products));
    return $products;
  }

  /* Consultar si existe producto en BD por compañia */

  public function findProduct($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT id_product FROM products
                                  WHERE reference = :reference
                                  AND product = :product 
                                  AND id_company = :id_company");
    $stmt->execute([
      'reference' => trim($dataProduct['referenceProduct']),
      'product' => ucfirst(strtolower(trim($dataProduct['product']))),
      'id_company' => $id_company
    ]);
    $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
    return $findProduct;
  }

  /* Insertar producto */
  public function insertProductByCompany($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      if (!isset($dataProduct['imgProduct'])) {
        $stmt = $connection->prepare("INSERT INTO products(id_company, reference, product, active) 
                                      VALUES(:id_company, :reference, :product, 1)");
        $stmt->execute([
          'reference' => trim($dataProduct['referenceProduct']),
          'product' => ucfirst(strtolower(trim($dataProduct['product']))),
          'id_company' => $id_company,
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
      } else {
        $stmt = $connection->prepare("INSERT INTO products(id_company, reference, product, img, active) 
                                      VALUES(:id_company, :reference, :product, :img, 1)");
        $stmt->execute([
          'id_company' => $id_company,
          'reference' => trim($dataProduct['referenceProduct']),
          'product' => ucfirst(strtolower(trim($dataProduct['product']))),
          'img' => $dataProduct['imgProduct'],
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();
      if ($e->getCode() == 23000)
        $message = 'La referencia ya existe. Ingrese una nueva referencia';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar producto */
  public function updateProductByCompany($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE products SET reference = :reference, product = :product 
                                    WHERE id_product = :id_product AND id_company = :id_company");
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => ucfirst(strtolower(trim($dataProduct['product']))),
        'id_product' => $dataProduct['idProduct'],
        'id_company' => $id_company
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function lastInsertedProductId($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $sql = "SELECT MAX(id_product) AS id_product FROM products WHERE id_company = :id_company";
    $query = $connection->prepare($sql);
    $query->execute(['id_company' => $id_company]);
    $id_product = $query->fetch($connection::FETCH_ASSOC);
    return $id_product;
  }


  public function imageProduct($id_product, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $targetDir = dirname(dirname(dirname(dirname(__DIR__)))) . '/assets/images/products/' . $id_company;
    $allowTypes = array('jpg', 'jpeg', 'png');

    $image_name = $_FILES['img']['name'];
    $tmp_name   = $_FILES['img']['tmp_name'];
    $size       = $_FILES['img']['size'];
    $type       = $_FILES['img']['type'];
    $error      = $_FILES['img']['error'];


    /* Verifica si directorio esta creado y lo crea */
    if (!is_dir($targetDir))
      mkdir($targetDir, 0777, true);

    $targetDir = '/api/src/assets/images/products/' . $id_company;
    $targetFilePath = $targetDir . '/' . $image_name;

    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if (in_array($fileType, $allowTypes)) {
      $sql = "UPDATE products SET img = :img WHERE id_product = :id_product AND id_company = :id_company";
      $query = $connection->prepare($sql);
      $query->execute([
        'img' => $targetFilePath,
        'id_product' => $id_product,
        'id_company' => $id_company
      ]);

      $targetDir = dirname(dirname(dirname(dirname(__DIR__)))) . '/assets/images/products/' . $id_company;
      $targetFilePath = $targetDir . '/' . $image_name;

      move_uploaded_file($tmp_name, $targetFilePath);
    }
  }

  public function deleteProduct($dataProduct)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM products WHERE id_product = :id_product");
    $stmt->execute(['id_product' => $dataProduct['idProduct']]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM products WHERE id_product = :id_product");
      $stmt->execute(['id_product' => $dataProduct['idProduct']]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }

  public function activeOrInactiveProducts($id_product, $active)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE products SET active = :active WHERE id_product = :id_product");
      $stmt->execute([
        'id_product' => $id_product,
        'active' => $active
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
