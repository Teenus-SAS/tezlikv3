<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CategoriesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCategories()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM categories");
        $stmt->execute();

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $categories = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("categories", array('categories' => $categories));
        return $categories;
    }

    public function findCategory($dataCategory)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_category FROM categories WHERE category = :category");
        $stmt->execute(['category' => ucfirst(strtolower(trim($dataCategory['category'])))]);
        $findCategory = $stmt->fetch($connection::FETCH_ASSOC);
        return $findCategory;
    }

    public function insertCategory($dataCategory)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO categories (category) VALUES (:category)");
            $stmt->execute(['category' => ucfirst(strtolower(trim($dataCategory['category'])))]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Categoría duplicado. Ingrese una nuevo categoría';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateCategory($dataCategory)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE categories SET category = :category WHERE id_category = :id_category");
            $stmt->execute([
                'category' => ucfirst(strtolower(trim($dataCategory['category']))),
                'id_category' => $dataCategory['idCategory'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteCategory($id_category)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM categories WHERE id_category = :id_category");
            $stmt->execute(['id_category' => $id_category]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM categories WHERE id_category = :id_category");
                $stmt->execute(['id_category' => $id_category]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Categoria asociada a un material. Imposible Eliminar';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
