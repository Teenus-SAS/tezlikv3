<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MinimumStockDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcMinimumStock($dataStock)
    {
        $connection = Connection::getInstance()->getconnection();
        $stmt = $connection->prepare("SELECT 
                                        (((us.jan + us.feb + us.mar + us.apr + us.may + us.jun + us.jul + us.aug + us.sept + us.oct + us.nov + us.dece) / 
                                        (pm.january + pm.february + pm.march + pm.april + pm.may + pm.june + pm.july + pm.august + pm.september + pm.october + pm.november + pm.december))*:lead_time) AS minimum_stock 
                                      FROM unit_sales us 
                                        INNER JOIN plan_cicles_machine cm ON cm.id_product = us.id_product 
                                        INNER JOIN plan_program_machines pm ON pm.id_machine = cm.id_machine 
                                      WHERE us.id_product = :id_product;");
        $stmt->execute([
            'id_product' => $dataStock['idProduct'],
            'lead_time' => $dataStock['leadTime']
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $minimumStock = $stmt->fetch($connection::FETCH_ASSOC);
        return $minimumStock;
    }
}
