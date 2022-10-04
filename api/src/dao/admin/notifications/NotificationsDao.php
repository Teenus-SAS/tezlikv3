<?php

namespace tezlikv3\dao;

use Exception;
use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class NotificationsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllNotifications()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT n.id_notification, n.id_company, n.description, c.company, n.date_notification
                                      FROM notifications n
                                        INNER JOIN companies c ON c.id_company = n.id_company");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $notifications = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("notifications", array('notifications' => $notifications));
        return $notifications;
    }

    public function insertNotification($dataNotifications)
    {
        $connection = Connection::getInstance()->getConnection();

        $date = date("Y-m-d");

        try {
            $stmt = $connection->prepare("INSERT INTO notifications (id_company, description, date_notification) 
                                          VALUES (:id_company, :descr, :date_notification)");
            $stmt->execute([
                'id_company' => $dataNotifications['company'],
                'descr' => $dataNotifications['description'],
                'date_notification' => $date
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateNotification($dataNotifications)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE notifications SET id_company = :id_company, description = :descr
                                          WHERE id_notification = :id_notification");
            $stmt->execute([
                'id_company' => $dataNotifications['company'],
                'descr' => $dataNotifications['description'],
                'id_notification' => $dataNotifications['idNotification']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteNotification($id_notification)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM notifications WHERE id_notification = :id_notification");
        $stmt->execute(['id_notification' => $id_notification]);
        $row = $stmt->rowCount();

        if ($row > 0) {
            $stmt = $connection->prepare("DELETE FROM notifications WHERE id_notification = :id_notification");
            $stmt->execute(['id_notification' => $id_notification]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
