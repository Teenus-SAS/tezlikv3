<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CalcRecoveryExpensesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /**
     * Calcula el porcentaje de gastos generales sobre ventas
     *
     * @param float $totalSales Ventas totales (debe ser mayor que 0)
     * @param float $overheadCosts Gastos generales (no puede ser negativo)
     * @param int $precision Número de decimales para redondeo (opcional)
     * @return float Porcentaje calculado
     * @throws \InvalidArgumentException Si los valores no son válidos
     */
    public function calculate(float $totalSales, float $overheadCosts, int $precision = 2): float
    {
        $this->validateInputs($totalSales, $overheadCosts);
        $percentage = ($overheadCosts / $totalSales) * 100;
        return round($percentage, $precision);
    }

    /**
     * Valida los valores de entrada
     *
     * @param float $totalSales
     * @param float $overheadCosts
     * @throws \InvalidArgumentException
     */
    private function validateInputs(float $totalSales, float $overheadCosts): void
    {
        if ($totalSales <= 0) {
            throw new \InvalidArgumentException(
                'Las ventas totales deben ser un valor positivo mayor que cero'
            );
        }

        if ($overheadCosts < 0) {
            throw new \InvalidArgumentException(
                'Los gastos generales no pueden ser un valor negativo'
            );
        }
    }
}
