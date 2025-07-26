<?php

namespace tezlikv3\dao;

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use PDO;
use InvalidArgumentException;
use RuntimeException;

class CalcRecoveryExpensesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /**
     * Calcula y almacena el porcentaje de gastos generales para productos del mes actual
     *
     * @param array $products Array de productos con id_product y created_at
     * @param float $totalSales Ventas totales del mes
     * @param float $overheadCosts Gastos generales del mes
     * @param int $id_company ID de la compañía
     * @param int $precision Número de decimales para redondeo
     * @return array Resultado del proceso
     * @throws InvalidArgumentException Si los valores no son válidos
     * @throws RuntimeException Si ocurre un error en la base de datos
     */
    public function calculateAndStore(array $products, float $totalSales, float $overheadCosts, int $id_company, int $precision = 2): array
    {
        $this->validateInputs($totalSales, $overheadCosts);
        $percentage = $this->calculatePercentage($totalSales, $overheadCosts, $precision);

        $processed = 0;
        $errors = [];

        try {
            foreach ($products as $product) {
                $this->storeExpenseRecovery($product['id_product'], $id_company, $percentage);
                $processed++;
            }

            return [
                'success' => true,
                'message' => "Porcentaje calculado y almacenado para $processed productos",
                'percentage' => $percentage,
                'processed' => $processed,
                'errors' => $errors
            ];
        } catch (\PDOException $e) {
            $this->logger->error('Error en calculateAndStore: ' . $e->getMessage());
            throw new RuntimeException('Error al guardar en base de datos', 0, $e);
        }
    }

    /**
     * Calcula el porcentaje de gastos generales sobre ventas
     */
    private function calculatePercentage(float $totalSales, float $overheadCosts, int $precision): float
    {
        return round(($overheadCosts / $totalSales) * 100, $precision);
    }

    /**
     * Almacena el porcentaje de recuperación de gastos para un producto
     */
    private function storeExpenseRecovery(int $id_product, int $id_company, float $percentage): void
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "INSERT INTO expenses_recover (id_product, id_company, expense_recover, updated_at)
                VALUES (:id_product, :id_company, :expense_recover, NOW())
                ON DUPLICATE KEY UPDATE 
                expense_recover = VALUES(expense_recover), 
                updated_at = VALUES(updated_at)";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_product' => $id_product,
            ':id_company' => $id_company,
            ':expense_recover' => $percentage
        ]);
    }

    /**
     * Valida los valores de entrada
     */
    private function validateInputs(float $totalSales, float $overheadCosts): void
    {
        if ($totalSales <= 0) {
            throw new InvalidArgumentException(
                'Las ventas totales deben ser un valor positivo mayor que cero'
            );
        }

        if ($overheadCosts < 0) {
            throw new InvalidArgumentException(
                'Los gastos generales no pueden ser un valor negativo'
            );
        }
    }
}
