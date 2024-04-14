<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LastDataDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Login */
    public function findLastLogins()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT cp.company, us.firstname, us.lastname, us.last_login 
                                      FROM users us INNER JOIN companies cp ON cp.id_company = us.id_company
                                      WHERE us.session_active = 1 ORDER BY us.last_login DESC");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $lastLogs = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Last Login", array('Last Login' => $lastLogs));

        return $lastLogs;
    }

    /* Compañia */
    public function findLastCompany()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_company) AS idCompany FROM companies");
        $stmt->execute();
        $lastId = $stmt->fetch($connection::FETCH_ASSOC);

        return $lastId;
    }

    /* Usuario */
    public function findLastInsertedUser($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_user) AS idUser FROM users u WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $user = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        return $user;
    }

    /* Productos */
    public function lastInsertedProductId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_product) AS id_product FROM products WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $id_product = $query->fetch($connection::FETCH_ASSOC);
        return $id_product;
    }

    /* Materiales */
    public function lastInsertedMaterialsId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_material) AS id_material FROM materials WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $id_material = $query->fetch($connection::FETCH_ASSOC);
        return $id_material;
    }

    /* Maquinas */
    public function lastInsertedMachineId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_machine) AS id_machine FROM machines WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $id_machine = $query->fetch($connection::FETCH_ASSOC);
        return $id_machine;
    }

    /* Procesos */
    public function lastInsertedProcessId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_process) AS id_process FROM process WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $id_process = $query->fetch($connection::FETCH_ASSOC);
        return $id_process;
    }

    /* Carga Fabril */
    public function findLastInsertedFactoryLoad($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_manufacturing_load) AS id_manufacturing_load FROM manufacturing_load WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $factoryload = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("factory load", array('factory load' => $factoryload));

        return $factoryload;
    }

    /* Carga Fabril */
    public function findLastInsertedProductProcess($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_product_process) AS id_product_process  FROM products_process WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productProcess = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("factory load", array('factory load' => $productProcess));

        return $productProcess;
    }

    /* Nomina */
    public function findLastInsertedPayroll($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_payroll) AS id_payroll  FROM payroll WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }

    /* Cotizaciones */
    public function findLastQuote()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_quote) AS id_quote FROM quotes");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quote = $stmt->fetch($connection::FETCH_ASSOC);
        return $quote;
    }

    public function findLastQuoteProducts()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_quote_product) AS id_quote_product FROM quotes_products");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quote = $stmt->fetch($connection::FETCH_ASSOC);
        return $quote;
    }

    public function findLastInsertedQCompany()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_quote_company) AS id_quote_company FROM quote_companies");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $company = $stmt->fetch($connection::FETCH_ASSOC);
        return $company;
    }

    public function findLastInsertedCustomPrice()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_custom_price) AS id_custom_price FROM custom_prices");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $company = $stmt->fetch($connection::FETCH_ASSOC);
        return $company;
    }
}
