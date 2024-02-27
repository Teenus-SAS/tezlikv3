<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ConversionUnitsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function convertUnits($dataMaterial, $dataProductMaterial, $quantity)
    {
        try {
            $magnitude = strtoupper(trim($dataProductMaterial['magnitude']));
            $unitProductMaterial = strtoupper(trim($dataProductMaterial['abbreviation']));
            $unitMaterial = $dataMaterial['abbreviation'];
            // $quantity = $dataProductMaterial['quantity'];

            $arr = [];

            if ($unitProductMaterial != $unitMaterial && $magnitude != 'UNIDAD') {

                switch ($magnitude) {
                    case 'LONGITUD':
                        $arr['M'] = array(
                            'CM' => array('value' => 0.01, 'op' => '*'), 'ML' => array('value' => 0.001, 'op' => '*'),
                            'INCH' => array('value' => 0.0254, 'op' => '*'), 'FT' => array('value' => 0.3048, 'op' => '*')
                        );
                        $arr['CM'] = array(
                            'M' => array('value' => 100, 'op' => '*'), 'ML' => array('value' => 0.1, 'op' => '*'),
                            'INCH' => array('value' => 2.54, 'op' => '*'), 'FT' => array('value' => 30.48, 'op' => '*')
                        );
                        $arr['ML'] = array(
                            'M' => array('value' => 1000, 'op' => '*'), 'CM' => array('value' => 10, 'op' => '*'),
                            'INCH' => array('value' => 25.4, 'op' => '*'), 'FT' => array('value' => 304.8, 'op' => '*')
                        );
                        $arr['INCH'] = array(
                            'M' => array('value' => 39.37007874, 'op' => '*'), 'CM' => array('value' => 0.3937007874, 'op' => '*'),
                            'ML' => array('value' => 0.0393700787, 'op' => '*'), 'FT' => array('value' => 12, 'op' => '*')
                        );
                        $arr['FT'] = array(
                            'M' => array('value' => 3.280839895, 'op' => '*'), 'CM' => array('value' => 0.032808399, 'op' => '*'),
                            'ML' => array('value' => 0.0032808399, 'op' => '*'), 'INCH' => array('value' => 0.0833333333, 'op' => '*')
                        );
                        break;
                    case 'MASA':
                        $arr['TN'] = array(
                            'KG' => array('value' => 0.001, 'op' => '*'), 'GR' => array('value' => 0.000001, 'op' => '*'),
                            'MG' => array('value' => 0.000000001, 'op' => '*'), 'LB' => array('value' => 0.0004535924, 'op' => '*')
                        );
                        $arr['KG'] = array(
                            'TN' => array('value' => 1000, 'op' => '*'), 'GR' => array('value' => 0.001, 'op' => '*'),
                            'MG' => array('value' => 0.000001, 'op' => '*'), 'LB' => array('value' => 0.45359237, 'op' => '*')
                        );
                        $arr['GR'] = array(
                            'TN' => array('value' => 1000000, 'op' => '*'), 'KG' => array('value' => 1000, 'op' => '*'),
                            'MG' => array('value' => 0.001, 'op' => '*'), 'LB' => array('value' => 453.59237, 'op' => '*')
                        );
                        $arr['MG'] = array(
                            'TN' => array('value' => 1000000000, 'op' => '*'), 'KG' => array('value' => 1000000, 'op' => '*'),
                            'GR' => array('value' => 1000, 'op' => '*'), 'LB' => array('value' => 453592.37, 'op' => '*')
                        );
                        $arr['LB'] = array(
                            'TN' => array('value' => 2204.6226218, 'op' => '*'), 'KG' => array('value' => 2.2046226218, 'op' => '*'),
                            'GR' => array('value' => 0.0022046226, 'op' => '*'), 'MG' => array('value' => 0.0000022046, 'op' => '*')
                        );
                        break;
                    case 'VOLUMEN':
                        $arr['CM3'] = array('M3' => array('value' => 1000000, 'op' => '*'), 'L' => array('value' => 1000, 'op' => '*'), 'ML' => array('value' => 1, 'op' => '*'), 'GL' => array('value' => 3785.41, 'op' => '*'));
                        $arr['M3'] = array('CM3' => array('value' => 0.000001, 'op' => '*'), 'L' => array('value' => 0.001, 'op' => '*'), 'ML' => array('value' => 0.000001, 'op' => '*'), 'GL' => array('value' => 0.00378541, 'op' => '*'));
                        $arr['L'] = array('CM3' => array('value' => 0.001, 'op' => '*'), 'M3' => array('value' => 1000, 'op' => '*'), 'ML' => array('value' => 0.001, 'op' => '*'), 'GL' => array('value' => 3.78541, 'op' => '*'));
                        $arr['ML'] = array('CM3' => array('value' => 1, 'op' => '*'), 'M3' => array('value' => 1000000, 'op' => '*'), 'L' => array('value' => 1000, 'op' => '*'), 'GL' => array('value' => 3785.41, 'op' => '*'));
                        $arr['GL'] = array('CM3' => array('value' => 0.000264172, 'op' => '/'), 'M3' => array('value' => 264.172, 'op' => '*'), 'L' => array('value' => 0.264172, 'op' => '*'), 'ML' => array('value' => 0.000264172, 'op' => '*'));
                        break;
                    case 'ÁREA':
                        $arr['DM2'] = array('M2' => array('value' => 100, 'op' => '*'), 'FT2' => array('value' => 9.2903043597, 'op' => '*'), 'INCH2' => array('value' => 0.0645160042, 'op' => '*'));
                        $arr['M2'] = array('DM2' => array('value' => 0.01, 'op' => '*'), 'FT2' => array('value' => 0.0929030436, 'op' => '*'), 'INCH2' => array('value' => 0.00064516, 'op' => '*'));
                        $arr['FT2'] = array('DM2' => array('value' => 0.1076391, 'op' => '*'), 'M2' => array('value' => 10.76391, 'op' => '*'), 'INCH2' => array('value' => 0.0069444446, 'op' => '*'));
                        $arr['INCH2'] = array('DM2' => array('value' => 15.50003, 'op' => '*'), 'M2' => array('value' => 1550.003, 'op' => '*'), 'FT2' => array('value' => 143.99999628, 'op' => '*'));
                        break;
                }

                $unit = $arr[$unitMaterial][$unitProductMaterial];
                $quantity = $quantity * $unit['value'];

                // $quantity = $this->calcQuantity($quantity, $unit['op'], $unit['value']);
                return $quantity;
            } else
                return $quantity;
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // public function calcQuantity($num1, $operator, $num2)
    // {
    //     if ($operator == '/')
    //         $value = $num1 / $num2;
    //     else if ($operator == '*')
    //         $value =  $num1 * $num2;

    //     return $value;
    // }
}
