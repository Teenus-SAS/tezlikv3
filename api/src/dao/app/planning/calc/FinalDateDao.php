<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FinalDateDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcFinalDate($dataMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT DATE_ADD(dm.start_dat, INTERVAL((:quantity * (pp.enlistment_time + pp.operation_time))/60) HOUR) AS final_date 
                                      FROM products p 
                                       INNER JOIN products_process pp ON pp.id_product = pp.id_product 
                                       INNER JOIN dates_machines dm ON dm.id_machine = pp.id_machine 
                                      WHERE dm.id_product = :id_product AND dm.id_machine = :id_machine AND dm.id_company = :id_company 
                                       AND p.id_product IN (SELECT id_product FROM products WHERE id_product = :id_product);");
        $stmt->execute([
            'quantity' => $dataMachine['quantity'],
            'id_product' => $dataMachine['idProduct'],
            'id_machine' => $dataMachine['idMachine'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $finalDate = $stmt->fetch($connection::FETCH_ASSOC);
        return $finalDate;
    }

    public function updateFinalDate($dataMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE dates_machines SET final_date = :final_date
                                          WHERE id_machine = :id_machine AND id_company = :id_company");
            $stmt->execute([
                'final_date' => $dataMachine['finalDate'],
                'id_machine' => $dataMachine['idMachine'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
