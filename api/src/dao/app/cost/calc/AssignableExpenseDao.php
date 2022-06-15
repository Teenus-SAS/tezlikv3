<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class AssignableExpenseDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcAssignableExpense($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        /* Consulta unidades vendidades y volumenes de venta por producto */

        $stmt = $connection->prepare("SELECT *
                                      FROM expenses_distribution 
                                      WHERE id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $UnitVol = $stmt->fetchAll($connection::FETCH_ASSOC);

        /* Calcular el total de unidades vendidas y volumen de ventas */

        $stmt = $connection->prepare("SELECT SUM(units_sold) as units_sold, SUM(turnover) as turnover 
                                      FROM expenses_distribution WHERE id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $totalUnitVol = $stmt->fetch($connection::FETCH_ASSOC);

        /* Obtener el total de gastos */

        $stmt = $connection->prepare("SELECT total_expense 
                                      FROM general_data 
                                      WHERE id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $totalExpense = $stmt->fetch($connection::FETCH_ASSOC);

        for ($i = 0; $i < sizeof($UnitVol); $i++) {
            /* Calcula el gasto asignable */
            $percentageUnitSolds =  $UnitVol[$i]['units_sold'] / $totalUnitVol['units_sold'];
            $percentageVolSolds = $UnitVol[$i]['turnover'] / $totalUnitVol['turnover'];
            $average = ($percentageUnitSolds + $percentageVolSolds) / 2;

            $averageExpense = $average * $totalExpense['total_expense'];
            $assignableExpense = $averageExpense / $UnitVol[$i]['units_sold'];

            /* Actualizar gasto asignable */
            $stmt = $connection->prepare("UPDATE expenses_distribution SET assignable_expense = :assignable_expense
                                      WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $UnitVol[$i]['id_product'],
                'assignable_expense' => $assignableExpense
            ]);
        }
    }
}
