<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPlanProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findProductByCategoryInProcess($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products WHERE reference = :reference
                                  AND product = :product AND category LIKE '%en proceso' AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataProduct['referenceProduct']),
            'product' => ucfirst(strtolower(trim($dataProduct['product']))),
            'id_company' => $id_company
        ]);
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }
}
