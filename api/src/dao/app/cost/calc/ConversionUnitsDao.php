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
                            'CM' => array('value' => 100, 'op' => '/'), 'ML' => array('value' => 1000, 'op' => '/'),
                            'INCH' => array('value' => 39.37, 'op' => '/'), 'FT' => array('value' => 3.281, 'op' => '/')
                        );
                        $arr['CM'] = array(
                            'M' => array('value' => 100, 'op' => '*'), 'ML' => array('value' => 10, 'op' => '/'),
                            'INCH' => array('value' => 2.54, 'op' => '*'), 'FT' => array('value' => 30.48, 'op' => '*')
                        );
                        $arr['ML'] = array(
                            'M' => array('value' => 1000, 'op' => '*'), 'CM' => array('value' => 10, 'op' => '*'),
                            'INCH' => array('value' => 25.4, 'op' => '*'), 'FT' => array('value' => 304.8, 'op' => '*')
                        );
                        $arr['INCH'] = array(
                            'M' => array('value' => 39.37, 'op' => '*'), 'CM' => array('value' => 2.54, 'op' => '/'),
                            'ML' => array('value' => 25.4, 'op' => '/'), 'FT' => array('value' => 12, 'op' => '*')
                        );
                        $arr['FT'] = array(
                            'M' => array('value' => 3.281, 'op' => '*'), 'CM' => array('value' => 38.48, 'op' => '/'),
                            'ML' => array('value' => 304.8, 'op' => '/'), 'INCH' => array('value' => 12, 'op' => '/')
                        );
                        break;
                    case 'MASA':
                        $arr['TN'] = array(
                            'KG' => array('value' => 1000, 'op' => '/'), 'GR' => array('value' => 1000000, 'op' => '/'),
                            'MG' => array('value' => 1000000000, 'op' => '/'), 'LB' => array('value' => 2205, 'op' => '/')
                        );
                        $arr['KG'] = array(
                            'TN' => array('value' => 1000, 'op' => '*'), 'GR' => array('value' => 1000, 'op' => '/'),
                            'MG' => array('value' => 1000000, 'op' => '/'), 'LB' => array('value' => 2.205, 'op' => '/')
                        );
                        $arr['GR'] = array(
                            'TN' => array('value' => 1000000, 'op' => '*'), 'KG' => array('value' => 1000, 'op' => '*'),
                            'MG' => array('value' => 1000, 'op' => '/'), 'LB' => array('value' => 453.6, 'op' => '*')
                        );
                        $arr['MG'] = array(
                            'TN' => array('value' => 1000000000, 'op' => '*'), 'KG' => array('value' => 1000000, 'op' => '*'),
                            'GR' => array('value' => 1000, 'op' => '*'), 'LB' => array('value' => 453600, 'op' => '*')
                        );
                        $arr['LB'] = array(
                            'TN' => array('value' => 2205, 'op' => '*'), 'KG' => array('value' => 2.205, 'op' => '*'),
                            'GR' => array('value' => 453.6, 'op' => '/'), 'MG' => array('value' => 1000, 'op' => '*')
                        );
                        break;
                    case 'VOLUMEN':
                        $arr['CM3'] = array('M3' => array('value' => 1000000, 'op' => '*'), 'L' => array('value' => 1000, 'op' => '*'), 'ML' => array('value' => 1, 'op' => '*'));
                        $arr['M3'] = array('CM3' => array('value' => 1000000, 'op' => '/'), 'L' => array('value' => 1000, 'op' => '/'), 'ML' => array('value' => 1000000, 'op' => '/'));
                        $arr['L'] = array('CM3' => array('value' => 1000, 'op' => '/'), 'M3' => array('value' => 1000, 'op' => '*'), 'ML' => array('value' => 1000, 'op' => '/'));
                        $arr['ML'] = array('CM3' => array('value' => 1, 'op' => '*'), 'M3' => array('value' => 1000000, 'op' => '*', 'L' => array('value' => 1000, 'op' => '*')));
                        break;
                    case 'AREA':
                        $arr['DM2'] = array('M2' => array('value' => 100, 'op' => '*'), 'FT2' => array('value' => 9.29, 'op' => '*'), 'INCH2' => array('value' => 15.5, 'op' => '/'));
                        $arr['M2'] = array('DM2' => array('value' => 100, 'op' => '/'), 'FT2' => array('value' => 10.764, 'op' => '/'), 'INCH2' => array('value' => 1550, 'op' => '/'));
                        $arr['FT2'] = array('DM2' => array('value' => 9.29, 'op' => '/'), 'M2' => array('value' => 10.764, 'op' => '*'), 'INCH2' => array('value' => 144, 'op' => '/'));
                        $arr['INCH2'] = array('DM2' => array('value' => 15.5, 'op' => '*'), 'M2' => array('value' => 1550, 'op' => '*'), 'FT2' => array('value' => 144, 'op' => '*'));
                        break;
                }

                $unit = $arr[$unitMaterial][$unitProductMaterial];

                $quantity = $this->calcQuantity($quantity, $unit['op'], $unit['value']);
                return $quantity;
            } else
                return $quantity;
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function calcQuantity($num1, $operator, $num2)
    {
        if ($operator == '/')
            $value = $num1 / $num2;
        else if ($operator == '*')
            $value =  $num1 * $num2;

        return $value;
    }
}
