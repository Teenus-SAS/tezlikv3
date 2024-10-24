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
    // Suma total por numero de cuenta Anual
    public function sumTotalExpenseAnualByNumberCount($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT LEFT(IFNULL(p.number_count, 0), 2) AS number_count, SUM(ex.expense_value) AS total_expense_value
                                          FROM expenses_anual ex
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
                                      FROM expenses_products_centers ecp
                                      	INNER JOIN expenses ex ON ex.id_expense = ecp.id_expense
                                        INNER JOIN puc p ON p.id_puc = ex.id_puc 
                                      WHERE ecp.id_company = :id_company
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

    // Obtener todos los gastos Anual
    public function findAllExpensesAnualByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT ex.id_expense_anual, p.number_count, ex.expense_value
                                      FROM expenses_anual ex
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
                                      FROM expenses_products_centers ecp
                                      	INNER JOIN expenses ex ON ex.id_expense = ecp.id_expense
                                        INNER JOIN puc p ON p.id_puc = ex.id_puc 
                                      WHERE ecp.id_company = :id_company
                                      ORDER BY `p`.`number_count` ASC");
        $stmt->execute(['id_company' => $id_company]);
        $expenseCount = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $expenseCount;
    }

    public function calcParticipationExpense($sumExpenseCount, $expenseCount, $op)
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
                if ($op == 2)
                    $this->updateParticipationExpenseAnual($expenseCount[$i]);
                else {
                    if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1)
                        $this->updateParticipationExpenseCP($expenseCount[$i]);
                    else
                        $this->updateParticipationExpense($expenseCount[$i]);
                }
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

    public function updateParticipationExpenseAnual($dataExpense)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE expenses_anual SET participation = :participation WHERE id_expense_anual = :id_expense_anual");
            $stmt->execute([
                'participation' => $dataExpense['participation'],
                'id_expense_anual' => $dataExpense['id_expense_anual']
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
