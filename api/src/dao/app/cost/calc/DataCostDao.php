<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DataCostDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcMinProfitability($data, $flag_expense)
    {
        $cost =
            floatval($data['cost_materials']) +
            floatval($data['cost_workforce']) +
            floatval($data['cost_indirect_cost']) +
            floatval($data['services']);

        if ($flag_expense == 0 || $flag_expense == 1) {
            $costTotal = $cost + $data['assignable_expense'];
        } elseif ($flag_expense == 2) {
            $costTotal = $cost / (1 - $data['expense_recover'] / 100);
        }

        if ($data['sale_price'] == 0)
            $profitability = 0;
        else
            $profitability = ((($data['sale_price'] * (1 - ($data['commission_sale'] / 100))) - $costTotal) / $data['sale_price']) * 100;

        return $profitability;
    }
    // public function getDataCost($data, $flag_expense)
    // {
    //     $cost =
    //         floatval($data['cost_materials']) +
    //         floatval($data['cost_workforce']) +
    //         floatval($data['cost_indirect_cost']) +
    //         floatval($data['services']);

    //     if ($flag_expense == 0 || $flag_expense == 1) {
    //         $expense = $data['assignable_expense'];
    //         $costTotal = $cost + $data['assignable_expense'];
    //     } elseif ($flag_expense == 2) {
    //         $costTotal = $cost / (1 - $data['expense_recover'] / 100);
    //         $expense = $costTotal * ($data['expense_recover'] / 100);
    //     }

    //     $pPrice = $costTotal / (1 - $data['profitability'] / 100);
    //     $price = $pPrice / (1 - $data['commission_sale'] / 100);

    //     $costProfitability = $pPrice * ($data['profitability'] / 100);

    //     $costCommissionSale = $price * ($data['commission_sale'] / 100);

    //     $profitability = ((($data['sale_price'] * (1 - ($data['commission_sale'] / 100))) - $costTotal) / $data['sale_price']) * 100;

    //     $costActualProfitability = $data['sale_price'] * ($profitability / 100);

    //     $dataCost = [
    //         'cost' => $cost,
    //         'costTotal' => $costTotal,
    //         'actualProfitability' => $profitability,
    //         'costCommissionSale' => $costCommissionSale,
    //         'costProfitability' => $costProfitability,
    //         'costActualProfitability' => $costActualProfitability,
    //         'expense' => $expense,
    //         'price' => $price,
    //     ];

    //     return $dataCost;
    // }
}
