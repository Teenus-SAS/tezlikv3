<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DashboardGeneralsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Obtener todos los Productos
    public function findAllProducts()
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT COUNT(id_product) AS products FROM products";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetch($connection::FETCH_ASSOC);
        return $products;
    }

    // Obtener cantidad de empresas activas
    public function findAllCompanies()
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) AS companies
                FROM companies c 
                JOIN companies_licenses cl ON cl.id_company = c.id_company
                WHERE YEAR(cl.license_end) = YEAR(CURDATE());";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $companies = $stmt->fetch($connection::FETCH_ASSOC);
        return $companies;
    }

    // Obtener todos los usuarios
    public function findAllUsersActive()
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT COUNT(id_user) AS users FROM users WHERE active = 1;";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetch($connection::FETCH_ASSOC);
        return $users;
    }

    // Obtener todos los Usuarios en sesión
    public function findAllActiveUsersSession()
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT COUNT(id_user) AS users_session FROM users WHERE session_active = 1";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $usersSessionActive = $stmt->fetch($connection::FETCH_ASSOC);
        return $usersSessionActive;
    }

    public function findAllCountByCompany()
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT c.company, COUNT(hu.id_historical) AS count,
                    ROUND(
                        COUNT(hu.id_historical) * 100.0 / (
                            SELECT COUNT(*) 
                            FROM historical_users hu2
                            WHERE YEAR(hu2.date) = YEAR(CURDATE())), 2) AS porcentaje_participacion
                FROM companies c 
                INNER JOIN users u ON u.id_company = c.id_company
                INNER JOIN historical_users hu ON hu.id_user = u.id_user
                WHERE YEAR(hu.date) = YEAR(CURDATE())
                GROUP BY c.id_company
                ORDER BY count DESC;";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $companies = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $companies;
    }

    public function findAllCompaniesAndUsers()
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT c.id_company, c.company, u.id_user, u.firstname, u.lastname, hu.date, 
                    DATE(hu.date) AS format_date, u.session_active
                FROM companies c
                INNER JOIN users u ON u.id_company = c.id_company
                INNER JOIN historical_users hu ON hu.id_user = u.id_user
                WHERE MONTH(hu.date) = MONTH(CURRENT_DATE()) AND YEAR(hu.date) = YEAR(CURDATE());";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companies = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("companies", array('companies' => $companies));
        return $companies;
    }

    public function findAllRecordsByYear()
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SET lc_time_names = 'es_ES'";
        $stmt = $connection->prepare($sql);
        $stmt->execute();

        $sql = "SELECT c.id_company, c.company, 
                    COUNT(hu.id_historical) AS total_registros,
                    CONCAT(UCASE(LEFT(MONTHNAME(hu.date), 1)), LOWER(SUBSTRING(MONTHNAME(hu.date), 2))) AS mes
                FROM historical_users hu
                INNER JOIN users u ON hu.id_user = u.id_user
                INNER JOIN companies c ON u.id_company = c.id_company
                WHERE YEAR(hu.date) = YEAR(CURDATE())
                GROUP BY c.id_company, c.company, mes
                ORDER BY total_registros DESC;";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $month = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $month;
    }

    /* public function findAllCountByYear()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SET lc_time_names = 'es_ES'");
        $stmt->execute();

        $stmt = $connection->prepare("SELECT c.id_company, c.company, u.id_user, u.firstname, u.lastname, hu.date, DAY(hu.date) AS day, DATE(hu.date) AS format_date, CONCAT(UCASE(LEFT(MONTHNAME(hu.date), 1)), LOWER(SUBSTRING(MONTHNAME(hu.date), 2))) AS month
                                      FROM companies c
                                        INNER JOIN users u ON u.id_company = c.id_company
                                        INNER JOIN historical_users hu ON hu.id_user = u.id_user
                                      WHERE YEAR(hu.date) = YEAR(CURRENT_DATE())");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $month = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("month", array('month' => $month));
        return $month;
    } */
}
