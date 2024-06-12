<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
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

    // Consulta unidades vendidades y volumenes de venta por producto
    public function findAllExpensesDistribution($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT ed.id_expenses_distribution, ed.id_product, ed.id_company, ed.units_sold, ed.turnover, ed.assignable_expense 
                                          FROM expenses_distribution ed 
                                            INNER JOIN products p ON p.id_product = ed.id_product 
                                            INNER JOIN products_costs pc ON pc.id_product = ed.id_product 
                                          WHERE ed.id_company = :id_company AND p.active = 1 -- AND pc.new_product = 0
                                          -- AND (ed.assignable_expense > 0 AND ed.units_sold > 0 AND ed.turnover > 0)
                                          AND (ed.units_sold > 0 AND ed.turnover > 0)
                                          ");
            $stmt->execute(['id_company' => $id_company]);
            $unitVol = $stmt->fetchAll($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $unitVol = array('info' => true, 'message' => $message);
        }
        return $unitVol;
    }

    // Consulta unidades vendidades y volumenes de venta por producto (anual)
    public function findAllExpensesDistributionAnual($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT ed.id_expense_distribution_anual, ed.id_product, ed.id_company, ed.units_sold, ed.turnover, ed.assignable_expense 
                                          FROM expenses_distribution_anual ed 
                                            INNER JOIN products p ON p.id_product = ed.id_product 
                                            INNER JOIN products_costs pc ON pc.id_product = ed.id_product 
                                          WHERE ed.id_company = :id_company AND p.active = 1 -- AND pc.new_product = 0
                                          -- AND (ed.assignable_expense > 0 AND ed.units_sold > 0 AND ed.turnover > 0)
                                          AND (ed.units_sold > 0 AND ed.turnover > 0)
                                          ");
            $stmt->execute(['id_company' => $id_company]);
            $unitVol = $stmt->fetchAll($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $unitVol = array('info' => true, 'message' => $message);
        }
        return $unitVol;
    }

    // Consulta unidades vendidades y volumenes de venta por centro produccion
    public function findAllExpensesDistributionByProduction($id_production_center)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT ed.id_expenses_distribution, ed.id_product, ed.id_company, ed.units_sold, ed.turnover, ed.assignable_expense 
                                          FROM expenses_distribution ed 
                                            INNER JOIN products p ON p.id_product = ed.id_product 
                                            INNER JOIN products_costs pc ON pc.id_product = ed.id_product 
                                          WHERE ed.id_production_center = :id_production_center AND p.active = 1
                                          AND (ed.units_sold > 0 AND ed.turnover > 0)");
            $stmt->execute(['id_production_center' => $id_production_center]);
            $unitVol = $stmt->fetchAll($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $unitVol = array('info' => true, 'message' => $message);
        }
        return $unitVol;
    }

    // Calcular el total de unidades vendidas y volumen de ventas
    public function findTotalUnitsVol($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT SUM(units_sold) as units_sold, SUM(turnover) as turnover 
                                          FROM expenses_distribution ed 
                                            INNER JOIN products p ON p.id_product = ed.id_product 
                                            INNER JOIN products_costs pc ON pc.id_product = ed.id_product 
                                          WHERE ed.id_company = :id_company AND p.active = 1 -- AND pc.new_product = 0
                                          ");
            $stmt->execute(['id_company' => $id_company]);
            $totalUnitVol = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $totalUnitVol = array('info' => true, 'message' => $message);
        }
        return $totalUnitVol;
    }
    // Calcular el total de unidades vendidas y volumen de ventas (anual)
    public function findTotalUnitsVolAnual($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT SUM(units_sold) as units_sold, SUM(turnover) as turnover 
                                          FROM expenses_distribution_anual ed 
                                            INNER JOIN products p ON p.id_product = ed.id_product 
                                            INNER JOIN products_costs pc ON pc.id_product = ed.id_product 
                                          WHERE ed.id_company = :id_company AND p.active = 1 -- AND pc.new_product = 0
                                          ");
            $stmt->execute(['id_company' => $id_company]);
            $totalUnitVol = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $totalUnitVol = array('info' => true, 'message' => $message);
        }
        return $totalUnitVol;
    }

    // Calcular el total de unidades vendidas y volumen de ventas por centro de produccion
    public function findTotalUnitsVolByProduction($id_production_center)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT SUM(units_sold) as units_sold, SUM(turnover) as turnover 
                                          FROM expenses_distribution ed 
                                            INNER JOIN products p ON p.id_product = ed.id_product 
                                            INNER JOIN products_costs pc ON pc.id_product = ed.id_product 
                                          WHERE ed.id_production_center = :id_production_center AND p.active = 1");
            $stmt->execute(['id_production_center' => $id_production_center]);
            $totalUnitVol = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $totalUnitVol = array('info' => true, 'message' => $message);
        }
        return $totalUnitVol;
    }

    // Calcular el total de unidades vendidas y volumen de ventas
    public function findTotalUnitsVolByFamily($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT IFNULL(SUM(units_sold), 0) as units_sold, IFNULL(SUM(turnover), 0) as turnover
                                          FROM families
                                          WHERE id_company = :id_company AND (assignable_expense > 0 OR units_sold > 0 OR turnover > 0)");
            $stmt->execute(['id_company' => $id_company]);
            $totalUnitVol = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $totalUnitVol = array('info' => true, 'message' => $message);
        }
        return $totalUnitVol;
    }

    // Obtener el total de gastos
    public function findTotalExpense($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT total_expense FROM general_data WHERE id_company = :id_company");
            $stmt->execute(['id_company' => $id_company]);
            $totalExpense = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $totalExpense = array('info' => true, 'message' => $message);
        }
        return $totalExpense;
    }

    // Obtener total de gastos por centro produccion
    public function findTotalExpenseByProduction($id_production_center)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT SUM(expense_value) AS expenses_value FROM expenses_products_centers WHERE id_production_center = :id_production_center");
            $stmt->execute(['id_production_center' => $id_production_center]);
            $totalExpense = $stmt->fetch($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $totalExpense = array('info' => true, 'message' => $message);
        }
        return $totalExpense;
    }
    // Obtener todo el total de gastos por centro produccion
    public function findAllTotalExpenseGroupByProduction($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT SUM(expense_value) AS expenses_value, id_production_center 
                                          FROM expenses_products_centers 
                                          WHERE id_company = :id_company
                                          GROUP BY id_production_center;");
            $stmt->execute(['id_company' => $id_company]);
            $totalExpense = $stmt->fetchAll($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $totalExpense = array('info' => true, 'message' => $message);
        }
        return $totalExpense;
    }

    // Función para calcular el porcentaje
    function calculatePercentage($numerator, $denominator)
    {
        return $denominator > 0 ? $numerator / $denominator : 0;
    }

    public function calcAssignableExpense($unitVol, $totalUnitVol, $totalExpense)
    {

        // Calcular los porcentajes
        $percentageUnitSolds = $this->calculatePercentage($unitVol['units_sold'], $totalUnitVol['units_sold']);
        $percentageVolSolds = $this->calculatePercentage($unitVol['turnover'], $totalUnitVol['turnover']);

        // Calcular el promedio
        $average = ($percentageUnitSolds + $percentageVolSolds) / 2;

        // Calcular el gasto promedio
        $averageExpense = $average * $totalExpense['total_expense'];

        // Calcular el gasto asignable
        $assignableExpense = $unitVol['units_sold'] > 0 ? $averageExpense / $unitVol['units_sold'] : 0;

        return array('averageExpense' => $averageExpense, 'assignableExpense' => $assignableExpense);
    }

    public function insertAssignableExpense($idProduct, $id_company, $assignableExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO expenses_distribution (id_product, id_company, assignable_expense) VALUES (:id_product, :id_company, :assignable_expense)");
            $stmt->execute([
                'id_product' => $idProduct,
                'id_company' => $id_company,
                'assignable_expense' => $assignableExpense
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Actualizar gasto asignable
    public function updateAssignableExpense($idProduct, $assignableExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE expenses_distribution SET assignable_expense = :assignable_expense WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $idProduct,
                'assignable_expense' => $assignableExpense
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
    // Actualizar gasto asignable
    public function updateAssignableExpenseAnual($idProduct, $assignableExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE expenses_distribution_anual SET assignable_expense = :assignable_expense WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $idProduct,
                'assignable_expense' => $assignableExpense
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Actualizar gasto asignable x familia
    public function updateAssignableExpenseByFamily($idFamily, $assignableExpense)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE families SET assignable_expense = :assignable_expense WHERE id_family = :id_family");
            $stmt->execute([
                'id_family' => $idFamily,
                'assignable_expense' => $assignableExpense
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
