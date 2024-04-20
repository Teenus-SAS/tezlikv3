<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCategoriesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findCategory($dataCategory, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT id_category FROM categories
                                  WHERE category = :category AND id_company = :id_company");
            $stmt->execute([
                'category' => strtoupper(trim($dataCategory['category'])),
                'id_company' => $id_company
            ]);
            $findCategory = $stmt->fetch($connection::FETCH_ASSOC);
            return $findCategory;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
