<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Consultar si existe producto en BD por compañia */
    public function findProduct($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products
                                  WHERE reference = :reference
                                  AND product = :product 
                                  AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataProduct['referenceProduct']),
            'product' => strtoupper(trim($dataProduct['product'])),
            'id_company' => $id_company
        ]);
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }

    /* Consultar si existe referencia o nombre de producto en BD por compañia */
    public function findProductByReferenceOrName($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products
                                      WHERE id_company = :id_company AND (reference = :reference OR product = :product)");
        $stmt->execute([
            'reference' => trim($dataProduct['referenceProduct']),
            'product' => strtoupper(trim($dataProduct['product'])),
            'id_company' => $id_company
        ]);
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }

    public function findProductCost($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.img, IFNULL(pc.price, 0) AS price, IFNULL(pc.sale_price, 0) AS sale_price, IFNULL(pc.cost_materials, 0) AS cost_materials
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

    public function findAllExpensesDistributionByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT ed.id_expenses_distribution, p.id_product, p.reference, p.product, IFNULL(ed.units_sold, 0) AS units_sold, IFNULL(ed.turnover, 0) AS turnover, ed.assignable_expense 
                                      FROM products p
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $expenses = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $expenses));
        return $expenses;
    }

    public function findAllExpenseRecoverByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT er.id_expense_recover, p.id_product, p.reference, p.product, IFNULL(er.expense_recover, 0) AS expense_recover
                                      FROM products p
                                        LEFT JOIN expenses_recover er ON p.id_product = er.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $recoverExpense = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("recoverExpense", array('recoverExpense' => $recoverExpense));
        return $recoverExpense;
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

    public function findAllProducts($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.id_product AS selectNameProduct, p.reference, p.product
                                      FROM products p
                                      INNER JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    // Modificar precio
    public function updatePrice($idProduct, $totalPrice)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("UPDATE products_costs SET price = :price WHERE id_product = :id_product");
        $stmt->execute([
            'price' => $totalPrice,
            'id_product' => $idProduct
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
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

    public function changeCompositeProduct($id_product, $composite)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products SET composite = :composite WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $id_product,
                'composite' => $composite
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
