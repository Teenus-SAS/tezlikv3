<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompaniesLicenseDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    //Obtener datos de licencia de empresas activas
    public function findCompanyLicenseActive()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                            -- Datos Compañia
                                                cp.id_company,
                                                cp.nit, 
                                                cp.company, 
                                                cl.license_start, 
                                                cl.license_end, 
                                                cl.quantity_user, 
                                                cl.license_status, 
                                            -- Accesos Compañia
                                                cl.inyection, 
                                                cl.flag_production_center, 
                                                cl.flag_expense_anual, 
                                                cl.flag_economy_scale, 
                                                cl.flag_sales_objective,
                                                cl.flag_price_objective,
                                                cl.plan, 
                                                cl.flag_currency_usd, 
                                                cl.flag_currency_eur, 
                                                cl.flag_employee, 
                                                cl.flag_composite_product, 
                                                cl.cost_historical, 
                                                cl.flag_indirect, 
                                                cl.flag_export_import, 
                                            -- Otros
                                                CASE WHEN cl.license_end > CURRENT_DATE THEN TIMESTAMPDIFF(DAY, CURRENT_DATE, license_end) ELSE 0 END AS license_days, 
                                                pa.cost_economy_scale, 
                                                pa.cost_sale_objectives,
                                                pa.cost_price_objectives
                                      FROM companies cp 
                                        INNER JOIN companies_licenses cl ON cp.id_company = cl.id_company
                                        INNER JOIN plans_access pa ON cl.plan = pa.id_plan
                                      WHERE cl.license_status = 1");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $licenses = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("licenses", array('licenses' => $licenses));

        return $licenses;
    }

    //Agregar Licencia
    public function addLicense($dataLicense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            if (empty($dataLicense['license_start'])) {
                $licenseStart = date('Y-m-d');
                $licenseEnd = date("Y-m-d", strtotime($licenseStart . "+ 30 day"));

                $stmt = $connection->prepare("INSERT INTO companies_licenses 
                                                (
                                                    -- Datos basicos licencia
                                                        id_company, 
                                                        license_start, 
                                                        license_end, 
                                                        quantity_user, 
                                                        license_status,
                                                    -- Accesos
                                                        plan, 
                                                        flag_currency_usd, 
                                                        flag_currency_eur, 
                                                        flag_employee, 
                                                        flag_composite_product, 
                                                        flag_economy_scale, 
                                                        flag_sales_objective, 
                                                        flag_price_objective, 
                                                        flag_production_center, 
                                                        flag_expense_anual, 
                                                        cost_historical, 
                                                        flag_indirect, 
                                                        flag_export_import, 
                                                        inyection
                                                )
                                              VALUES 
                                                (
                                                    -- Datos basicos licencia
                                                        :id_company, 
                                                        :license_start, 
                                                        :license_end, 
                                                        :quantity_user, 
                                                        :license_status, 
                                                    -- Accesos
                                                        :plan, 
                                                        :flag_currency_usd, 
                                                        :flag_currency_eur, 
                                                        :flag_employee, 
                                                        :flag_composite_product, 
                                                        :flag_economy_scale, 
                                                        :flag_sales_objective, 
                                                        :flag_price_objective, 
                                                        :flag_production_center, 
                                                        :flag_expense_anual, 
                                                        :cost_historical, 
                                                        :flag_indirect, 
                                                        :flag_export_import, 
                                                        :inyection
                                                )
                                            ");
                $stmt->execute([
                    'id_company' => $id_company,
                    'license_start' => $licenseStart,
                    'license_end' => $licenseEnd,
                    'quantity_user' => 1,
                    'license_status' => 1,
                    'plan' => 1,
                    'flag_currency_usd' => 1,
                    'flag_currency_eur' => 1,
                    'flag_employee' => 1,
                    'flag_composite_product' => 1,
                    'flag_economy_scale' => 1,
                    'flag_sales_objective' => 1,
                    'flag_price_objective' => 1,
                    'flag_production_center' => 1,
                    'flag_expense_anual' => 1,
                    'cost_historical' => 1,
                    'flag_indirect' => 1,
                    'flag_export_import' => 1,
                    'inyection' => 1
                ]);
            } else {
                $stmt = $connection->prepare("INSERT INTO companies_licenses 
                                                (
                                                    -- Datos Basicos licencia
                                                        id_company, 
                                                        license_start, 
                                                        license_end, 
                                                        quantity_user, 
                                                        license_status, 
                                                    -- Accesos
                                                        plan,
                                                        flag_currency_usd, 
                                                        flag_currency_eur, 
                                                        flag_employee, 
                                                        flag_composite_product, 
                                                        flag_economy_scale, 
                                                        flag_sales_objective, 
                                                        flag_price_objective, 
                                                        flag_production_center, 
                                                        flag_expense_anual, 
                                                        cost_historical, 
                                                        flag_indirect, 
                                                        flag_export_import, 
                                                        inyection
                                                )
                                              VALUES 
                                                (
                                                    -- Datos Basicos licencia
                                                        :id_company, 
                                                        :license_start, 
                                                        :license_end, 
                                                        :quantity_user, 
                                                        :license_status,
                                                    -- Accesos
                                                        :plan,  
                                                        :flag_currency_usd, 
                                                        :flag_currency_eur, 
                                                        :flag_employee, 
                                                        :flag_composite_product, 
                                                        :flag_economy_scale, 
                                                        :flag_sales_objective, 
                                                        :flag_price_objective, 
                                                        :flag_production_center, 
                                                        :flag_expense_anual, 
                                                        :cost_historical, 
                                                        :flag_indirect, 
                                                        :flag_export_import, 
                                                        :inyection
                                                )
                                            ");
                $stmt->execute([
                    'id_company' => $id_company,
                    'license_start' => $dataLicense['license_start'],
                    'license_end' => $dataLicense['license_end'],
                    'quantity_user' => $dataLicense['quantityUsers'],
                    'license_status' => 1,
                    'plan' => $dataLicense['plan'],
                    'flag_currency_usd' => $dataLicense['currencyUSD'],
                    'flag_currency_eur' => $dataLicense['currencyEUR'],
                    'flag_employee' => $dataLicense['payrollEmployee'],
                    'flag_composite_product' => $dataLicense['compositeProducts'],
                    'flag_economy_scale' => $dataLicense['economyScale'],
                    'flag_sales_objective' => $dataLicense['salesObjective'],
                    'flag_price_objective' => $dataLicense['priceObjective'],
                    'flag_production_center' => $dataLicense['production'],
                    'flag_expense_anual' => $dataLicense['anualExpenses'],
                    'cost_historical' => $dataLicense['historical'],
                    'flag_indirect' => $dataLicense['indirect'],
                    'flag_export_import' => $dataLicense['exportImport'],
                    'inyection' => $dataLicense['inyection'],
                ]);
            }

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Compañia duplicada, ingrese otra compañia';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    //Actualizar Licencia
    public function updateLicense($dataLicense)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET 
                                            -- Datos Basicos Licencia
                                                license_start = :license_start, 
                                                license_end = :license_end, 
                                                quantity_user = :quantity_user,
                                            -- Accesos
                                                plan = :plan, 
                                                inyection = :inyection, 
                                                flag_production_center = :flag_production_center, 
                                                flag_expense_anual = :flag_expense_anual, 
                                                flag_economy_scale = :flag_economy_scale,
                                                flag_sales_objective = :flag_sales_objective,
                                                flag_price_objective = :flag_price_objective,
                                                flag_currency_usd = :flag_currency_usd, 
                                                flag_currency_eur = :flag_currency_eur, 
                                                flag_employee = :flag_employee, 
                                                flag_composite_product = :flag_composite_product, 
                                                cost_historical = :cost_historical, 
                                                flag_indirect = :flag_indirect, 
                                                flag_export_import = :flag_export_import
                                          WHERE id_company = :id_company");
            $stmt->execute([
                'license_start' => $dataLicense['license_start'],
                'license_end' => $dataLicense['license_end'],
                'quantity_user' => $dataLicense['quantityUsers'],
                'plan' => $dataLicense['plan'],
                'id_company' => $dataLicense['company'],
                'flag_currency_usd' => $dataLicense['currencyUSD'],
                'flag_currency_eur' => $dataLicense['currencyEUR'],
                'flag_employee' => $dataLicense['payrollEmployee'],
                'flag_composite_product' => $dataLicense['compositeProducts'],
                'flag_economy_scale' => $dataLicense['economyScale'],
                'flag_sales_objective' => $dataLicense['salesObjective'],
                'flag_price_objective' => $dataLicense['priceObjective'],
                'flag_production_center' => $dataLicense['production'],
                'flag_expense_anual' => $dataLicense['anualExpenses'],
                'cost_historical' => $dataLicense['historical'],
                'flag_indirect' => $dataLicense['indirect'],
                'flag_export_import' => $dataLicense['exportImport'],
                'inyection' => $dataLicense['inyection']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteCompanyDemo()
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("DELETE cl, c
                                          FROM companies_licenses cl
                                            INNER JOIN companies c ON c.id_company = cl.id_company
                                          WHERE c.company = 'Demo' AND cl.license_end < CURRENT_DATE");
            $stmt->execute();
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
