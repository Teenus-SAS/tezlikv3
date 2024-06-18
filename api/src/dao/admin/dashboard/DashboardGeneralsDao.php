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
        $stmt = $connection->prepare("SELECT COUNT(id_product) AS products FROM products");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("ProductsCount", array('ProductsCount' => $products));
        return $products;
    }

    // Obtener cantidad de empresas activas
    public function findAllCompanies()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_company) AS companies FROM companies");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companies = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("CompaniesCount", array('CompaniesCount' => $companies));
        return $companies;
    }

    // Obtener todos los usuarios
    public function findAllUsers()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_user) AS users FROM users");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("UsersCount", array('UsersCount' => $users));
        return $users;
    }

    // Obtener todos los Usuarios en sesión
    public function findAllActiveUsersSession()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_user) AS users_session FROM users WHERE session_active = 1");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $usersSessionActive = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("UsersSessionActiveCount", array('UsersSessionActiveCount' => $usersSessionActive));
        return $usersSessionActive;
    }

    public function findAllCountByCompany()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT c.company, COUNT(hu.id_historical) AS count
                                      FROM companies c 
                                      	INNER JOIN users u ON u.id_company = c.id_company
                                        INNER JOIN historical_users hu ON hu.id_user = u.id_user
                                        GROUP BY c.id_company ORDER BY `count` DESC");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companies = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("companies", array('companies' => $companies));
        return $companies;
    }

    public function findAllComaniesAndUsers()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT c.id_company, c.company, u.id_user, u.firstname, u.lastname, hu.date, DATE(hu.date) AS format_date, u.session_active
                                       FROM companies c
                                         INNER JOIN users u ON u.id_company = c.id_company
                                         INNER JOIN historical_users hu ON hu.id_user = u.id_user
                                      WHERE MONTH(hu.date) = MONTH(CURRENT_DATE())");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companies = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("companies", array('companies' => $companies));
        return $companies;
    }

    public function findAllCountByMonth()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SET lc_time_names = 'es_ES'");
        $stmt->execute();

        $stmt = $connection->prepare("SELECT c.id_company, c.company, u.id_user, u.firstname, u.lastname, hu.date, DAY(hu.date) AS day, DATE(hu.date) AS format_date, CONCAT(UCASE(LEFT(MONTHNAME(hu.date), 1)), LOWER(SUBSTRING(MONTHNAME(hu.date), 2))) AS month
                                      FROM companies c
                                        INNER JOIN users u ON u.id_company = c.id_company
                                        INNER JOIN historical_users hu ON hu.id_user = u.id_user
                                      WHERE MONTH(hu.date) = MONTH(CURRENT_DATE())");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $month = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("month", array('month' => $month));
        return $month;
    }

    public function findAllCountByYear()
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
    }
}
