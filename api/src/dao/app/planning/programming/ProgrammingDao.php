<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProgrammingDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAExistingProductAndMachine($dataProgramming, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_cicles_machine pcm 
                                      INNER JOIN products_process pp ON pp.id_product = pcm.id_product 
                                      WHERE pcm.id_product = :id_product AND pcm.id_machine = :id_machine AND pcm.id_company = :id_company;");
        $stmt->execute([
            'id_product' => $dataProgramming['idProduct'],
            'id_machine' => $dataProgramming['idMachine'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }
}
