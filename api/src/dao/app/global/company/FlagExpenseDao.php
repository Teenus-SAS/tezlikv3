<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FlagExpenseDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function updateFlagExpense($flag_expense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("UPDATE companies_licenses SET flag_expense = :flag_expense WHERE id_company = :id_company");
        $stmt->execute([
            'flag_expense' => $flag_expense,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
}
