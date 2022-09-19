<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ClassificationDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcInventoryABC($dataInventory, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // Calcular rotación, ventas al año y promedio unidades 
        $stmt = $connection->prepare("SELECT ((IF(jan > 0, 1, 0) + IF(feb > 0, 1, 0) + IF(mar > 0, 1, 0) + IF(apr > 0, 1, 0) + 
                                               IF(may > 0, 1, 0) + IF(jun > 0, 1, 0) + IF(jul > 0, 1, 0) + IF(aug > 0, 1, 0) + 
                                               IF(sept > 0, 1, 0) + IF(oct > 0, 1, 0) + IF(nov > 0, 1, 0) + IF(dece > 0, 1, 0)) / :cant_months) AS year_sales,
                                             ((jan + feb + mar + apr + may + jun + jul + aug + sept + oct + nov + dece)/:cant_months) AS average_units
                                      FROM plan_unit_sales 
                                      WHERE id_product = :id_product AND id_company = :id_company;");
        $stmt->execute([
            'cant_months' => $dataInventory['cantMonths'],
            'id_product' => $dataInventory['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $inventoryABC = $stmt->fetch($connection::FETCH_ASSOC);
        return $inventoryABC;
    }

    public function calcClassificationByProduct($dataInventory, $id_company)
    {
        // Calcular Ventas al año
        $inventoryABC = $this->calcInventoryABC($dataInventory, $id_company);

        if ($inventoryABC) {
            /* Crear Clasificación */
            if ($inventoryABC['year_sales'] > 0.83) $dataInventory['classification'] = 'A';
            else if ($inventoryABC['year_sales'] >= 0.50) $dataInventory['classification'] = 'B';
            else $dataInventory['classification'] = 'C';

            // Modificar clasificación en tabla products
            $this->updateProductClassification($dataInventory);
        } else {
            return 1;
        }
    }

    public function updateProductClassification($dataInventory)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products SET classification = :classification WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $dataInventory['idProduct'],
                'classification' => $dataInventory['classification']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
