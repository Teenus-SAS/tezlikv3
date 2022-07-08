<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UnitSalesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllSalesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT u.id_unit_sales, u.id_product, p.product, u.year, u.jan, u.feb, u.mar, u.apr, u.may, u.jun, u.jul, u.aug, u.sept, u.oct, u.nov, u.dece 
                                      FROM unit_sales u 
                                        INNER JOIN products p ON p.id_product = u.id_product 
                                      WHERE u.id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $sales = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Ventas", array('Ventas' => $sales));
        return $sales;
    }

    public function findSales($dataSale, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_unit_sales FROM unit_sales
                                  WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataSale['idProduct'],
            'id_company' => $id_company
        ]);
        $findSales = $stmt->fetch($connection::FETCH_ASSOC);
        return $findSales;
    }

    public function insertSalesByCompany($dataSale, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $year =  date('Y');

        try {
            $stmt = $connection->prepare("INSERT INTO unit_sales (id_company, id_product, year, jan, feb, mar, apr, may, jun, jul, aug, sept, oct, nov, dece) 
                                          VALUES (:id_company, :id_product, :year, :jan, :feb, :mar, :apr, :may, :jun, :jul, :aug, :sept, :oct, :nov, :dece)");
            $stmt->execute([
                'id_company'  => $id_company,                       'jun' => $dataSale['june'],
                'id_product' => $dataSale['idProduct'],             'jul' => $dataSale['july'],
                'year' => $year,                                    'aug' => $dataSale['august'],
                'jan' => $dataSale['january'],                      'sept' => $dataSale['september'],
                'feb' => $dataSale['february'],                     'oct' => $dataSale['october'],
                'mar' => $dataSale['march'],                        'nov' => $dataSale['november'],
                'apr' => $dataSale['april'],                        'dece' => $dataSale['december'],
                'may' => $dataSale['may'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateSales($dataSale)
    {
        $connection = Connection::getInstance()->getConnection();

        $year =  date('Y');

        try {
            $stmt = $connection->prepare("UPDATE unit_sales SET id_product = :id_product, year = :year, jan = :jan, feb = :feb, mar = :mar, apr = :apr, may = :may, 
                                                            jun = :jun, jul = :jul, aug = :aug, sept = :sept, oct = :oct, nov = :nov, dece = :dece
                                          WHERE id_unit_sales = :id_unit_sales");
            $stmt->execute([
                'id_unit_sales' => $dataSale['idSale'],             'jun' => $dataSale['june'],
                'id_product' => $dataSale['idProduct'],     'jul' => $dataSale['july'],
                'year' => $year,                                    'aug' => $dataSale['august'],
                'jan' => $dataSale['january'],                      'sept' => $dataSale['september'],
                'feb' => $dataSale['february'],                     'oct' => $dataSale['october'],
                'mar' => $dataSale['march'],                        'nov' => $dataSale['november'],
                'apr' => $dataSale['april'],                        'dece' => $dataSale['december'],
                'may' => $dataSale['may'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteSale($id_unit_sales)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM unit_sales WHERE id_unit_sales = :id_unit_sales");
            $stmt->execute(['id_unit_sales' => $id_unit_sales]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM unit_sales WHERE id_unit_sales = :id_unit_sales");
                $stmt->execute(['id_unit_sales' => $id_unit_sales]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
