<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class BinnacleDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllBinnacle()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.firstname, u.lastname, u.email, b.date_binnacle, b.activity_performed, b.actual_information, b.previous_information 
                                      FROM binnacle b 
                                        INNER JOIN users u ON b.id_user = u.id_user 
                                      ORDER BY b.date_binnacle DESC");
        $stmt->execute();

        $binnacle = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $binnacle;
    }
}
