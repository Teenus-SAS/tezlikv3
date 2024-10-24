<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ReportCostDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCostWorkforceByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pt.reference, pt.product, p.id_process, IFNULL((SELECT IFNULL(SUM(IFNULL(py.minute_value, 0) *(IFNULL((IFNULL(pr.enlistment_time, 0) + IFNULL(pr.operation_time, 0)) / IFNULL((IF(pr.efficiency = 0, 100, pr.efficiency) / 100), 0), 0))), 0)
                                                                                            FROM payroll py INNER JOIN products_process pr ON pr.id_process = py.id_process AND pr.auto_machine = 0 
                                                                                        WHERE pr.id_product = pp.id_product AND pr.id_process = p.id_process), 0) AS workforce
                                      FROM process p
                                            INNER JOIN products_process pp ON pp.id_process = p.id_process
                                            INNER JOIN products pt ON pt.id_product = pp.id_product
                                      WHERE pp.id_company = :id_company AND pt.active = 1
                                      ORDER BY `pt`.`product` ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $costWorkforce = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("costWorkforce", array('costWorkforce' => $costWorkforce));
        return $costWorkforce;
    }
}
