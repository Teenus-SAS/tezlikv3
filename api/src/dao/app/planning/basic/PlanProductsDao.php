<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PlanProductsDao
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
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, p.img, p.quantity, m.mold, p.classification
                                  FROM products p
                                    INNER JOIN inv_molds m ON m.id_mold = p.id_mold
                                  WHERE p.id_company = :id_company");
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

    /* if (!empty($dataProduct['img'])) { */
    try {
      $stmt = $connection->prepare("INSERT INTO products(id_company, reference, product, id_mold, quantity) 
                                      VALUES(:id_company, :reference, :product, :id_mold, :quantity)");
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => ucfirst(strtolower(trim($dataProduct['product']))),
        'id_mold' => $dataProduct['idMold'],
        'id_company' => $id_company,
        'quantity' => $dataProduct['quantity']
      ]);

      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      if ($e->getCode() == 23000)
        $message = 'Referencia duplicada. Ingrese una nueva referencia';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar producto */
  public function updateProductByCompany($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE products SET reference = :reference, product = :product, id_mold = :id_mold, quantity = :quantity 
                                    WHERE id_product = :id_product AND id_company = :id_company");
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => ucfirst(strtolower(trim($dataProduct['product']))),
        'id_mold' => $dataProduct['idMold'],
        'id_company' => $id_company,
        'quantity' => $dataProduct['quantity'],
        'id_product' => $dataProduct['idProduct'],
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

  public function deleteProduct($id_product)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT * FROM products WHERE id_product = :id_product");
    $stmt->execute(['id_product' => $id_product]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM products WHERE id_product = :id_product");
      $stmt->execute(['id_product' => $id_product]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
