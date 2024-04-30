<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ParticipationExpenseDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Suma total por numero de cuenta
    public function sumTotalExpenseByNumberCount($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT LEFT(IFNULL(p.number_count, 0), 2) AS number_count, SUM(ex.expense_value) AS total_expense_value
                                          FROM expenses ex
                                          INNER JOIN puc p ON p.id_puc = ex.id_puc
                                          WHERE ex.id_company = :id_company
                                          GROUP BY LEFT(p.number_count, 2)
                                          ORDER BY LEFT(p.number_count, 2) ASC");
        $stmt->execute(['id_company' => $id_company]);
        $sumExpenseCount = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $sumExpenseCount;
    }

    // Suma total por numero de cuenta unidades de produccion
    public function sumTotalExpenseByNumberCountCP($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT LEFT(IFNULL(p.number_count, 0), 2) AS number_count, SUM(ex.expense_value) AS total_expense_value
                                      FROM expenses ex
                                        INNER JOIN puc p ON p.id_puc = ex.id_puc
                                      LEFT JOIN expenses_products_centers ecp ON ecp.id_expense = ex.id_expense
                                      WHERE ex.id_company = :id_company
                                        GROUP BY LEFT(p.number_count, 2)
                                        ORDER BY LEFT(p.number_count, 2) ASC");
        $stmt->execute(['id_company' => $id_company]);
        $sumExpenseCount = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $sumExpenseCount;
    }

    // Obtener todos los gastos
    public function findAllExpensesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT ex.id_expense, p.number_count, ex.expense_value
                                      FROM expenses ex
                                        INNER JOIN puc p ON p.id_puc = ex.id_puc
                                      WHERE ex.id_company = :id_company
                                      ORDER BY `p`.`number_count` ASC");
        $stmt->execute(['id_company' => $id_company]);
        $expenseCount = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $expenseCount;
    }

    // Obtener todos los gastos unidades produccion
    public function findAllExpensesByCompanyCP($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT ecp.id_expense_product_center, p.number_count, ex.expense_value
                                      FROM expenses ex
                                        INNER JOIN puc p ON p.id_puc = ex.id_puc
                                        LEFT JOIN expenses_products_centers ecp ON ecp.id_expense = ex.id_expense
                                      WHERE ex.id_company = :id_company
                                      ORDER BY `p`.`number_count` ASC");
        $stmt->execute(['id_company' => $id_company]);
        $expenseCount = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $expenseCount;
    }

    public function calcParticipationExpense($sumExpenseCount, $expenseCount)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            // Capturar todas las cuentas generales
            $stmt = $connection->prepare("SELECT * FROM puc WHERE LENGTH(number_count) = 2");
            $stmt->execute();
            $generalsPucs = $stmt->fetchAll($connection::FETCH_ASSOC);

            // Crear array con sus respectivas numeros cuentas generales
            for ($i = 0; $i < sizeof($sumExpenseCount); $i++) {
                $count[$sumExpenseCount[$i]['number_count']] = $sumExpenseCount[$i]['total_expense_value'];
            }

            // Calculo de porcentaje
            for ($i = 0; $i < sizeof($expenseCount); $i++) {
                $totalExpenseCount = 0;

                foreach ($generalsPucs as $arr) {
                    if (substr($expenseCount[$i]['number_count'], 0, 2) == $arr['number_count']) {
                        $totalExpenseCount = $count[$arr['number_count']];
                        break;
                    }
                }

                $expenseCount[$i]['participation'] = ($expenseCount[$i]['expense_value'] / $totalExpenseCount) * 100;

                // Modificar
                if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1)
                    $this->updateParticipationExpense($expenseCount[$i]);
                else
                    $this->updateParticipationExpenseCP($expenseCount[$i]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateParticipationExpense($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE expenses SET participation = :participation WHERE id_expense = :id_expense");
            $stmt->execute([
                'participation' => $dataExpense['participation'],
                'id_expense' => $dataExpense['id_expense']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateParticipationExpenseCP($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE expenses_products_centers SET participation = :participation WHERE id_expense_product_center = :id_expense_product_center");
            $stmt->execute([
                'participation' => $dataExpense['participation'],
                'id_expense_product_center' => $dataExpense['id_expense_product_center']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
