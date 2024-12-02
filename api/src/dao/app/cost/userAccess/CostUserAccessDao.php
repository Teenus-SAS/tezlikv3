<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CostUserAccessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUsersAccess($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        // $rol = $_SESSION['rol'];

        // if ($rol == 2) {
        $stmt = $connection->prepare("SELECT 
                                            -- Información Usuario
                                                us.id_user, 
                                                us.firstname, 
                                                us.lastname, 
                                                us.email,
                                                us.active,
                                            -- Menú Maestros
                                                IFNULL(usa.create_product, 0) AS create_product, 
                                                IFNULL(usa.create_materials, 0) AS create_materials, 
                                                IFNULL(usa.create_machines, 0) AS create_machines, 
                                                IFNULL(usa.create_process, 0) AS create_process, 
                                            -- Menú Configuración   
                                                IFNULL(usa.product_materials, 0) AS product_materials, 
                                                IFNULL(usa.export_import, 0) AS export_import, 
                                                IFNULL(usa.factory_load, 0) AS factory_load, 
                                                IFNULL(usa.external_service, 0) AS external_service, 
                                                IFNULL(usa.custom_price, 0) AS custom_price, 
                                            -- Menú General
                                                IFNULL(usa.payroll_load, 0) AS payroll_load, 
                                                IFNULL(usa.type_payroll, 0) AS type_payroll, 
                                                IFNULL(usa.expense, 0) AS expense, 
                                                IFNULL(usa.expense_distribution, 0) AS expense_distribution,
                                                IFNULL(usa.production_center, 0) AS production_center,
                                                IFNULL(usa.anual_expense, 0) AS anual_expense,
                                                IFNULL(usa.type_expense, 0) AS type_expense,  
                                            -- Menú Administrador
                                                IFNULL(usa.user, 0) AS user, 
                                                IFNULL(usa.backup, 0) AS backup, 
                                                IFNULL(usa.general_cost_report, 0) AS general_cost_report, 
                                            -- Menú Cotizacion
                                                IFNULL(usa.quote_payment_method, 0) AS quote_payment_method, 
                                                IFNULL(usa.quote_company, 0) AS quote_company, 
                                                IFNULL(usa.quote_contact, 0) AS quote_contact, 
                                            -- Navegador Lista de Precios
                                                IFNULL(usa.price, 0) AS price, 
                                                IFNULL(usa.price_usd, 0) AS price_usd, 
                                                IFNULL(usa.type_custom_price, 0) AS type_custom_price,
                                            -- Navegador Herramientas
                                                IFNULL(usa.analysis_material, 0) AS analysis_material, 
                                                IFNULL(usa.economy_scale, 0) AS economy_scale,
                                                IFNULL(usa.sale_objectives, 0) AS sale_objectives,
                                                IFNULL(usa.price_objectives, 0) AS price_objectives,
                                                IFNULL(usa.multiproduct, 0) AS multiproduct,
                                                IFNULL(usa.historical, 0) AS historical,
                                                IFNULL(usa.simulator, 0) AS simulator, 
                                            -- Navegador Otros
                                                IFNULL(usa.support, 0) AS support, 
                                                IFNULL(usa.quote, 0) AS quote, 
                                                IFNULL(usa.contract, 0) AS contract
                                          FROM users us
                                          LEFT JOIN cost_users_access usa ON usa.id_user = us.id_user
                                          WHERE us.id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
        // }
    }

    public function findUserAccess($id_company, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();
        // IFNULL(usa.price_usd, 0) AS price_usd, 
        $stmt = $connection->prepare("SELECT 
                                            -- Información Usuario
                                                us.id_user, 
                                                us.firstname, 
                                                us.lastname, 
                                                us.email, 
                                            -- Menú Maestros
                                                IFNULL(usa.create_product, 0) AS create_product, 
                                                IFNULL(usa.create_materials, 0) AS create_materials, 
                                                IFNULL(usa.create_machines, 0) AS create_machines, 
                                                IFNULL(usa.create_process, 0) AS create_process, 
                                            -- Menú Configuración   
                                                IFNULL(usa.product_materials, 0) AS product_materials, 
                                                IFNULL(usa.export_import, 0) AS export_import, 
                                                IFNULL(usa.external_service, 0) AS external_service, 
                                                IFNULL(usa.factory_load, 0) AS factory_load, 
                                                IFNULL(usa.custom_price, 0) AS custom_price, 
                                            -- Menú General
                                                IFNULL(usa.payroll_load, 0) AS payroll_load, 
                                                IFNULL(usa.type_payroll, 0) AS type_payroll, 
                                                IFNULL(usa.expense, 0) AS expense, 
                                                IFNULL(usa.expense_distribution, 0) AS expense_distribution,
                                                IFNULL(usa.production_center, 0) AS production_center,
                                                IFNULL(usa.anual_expense, 0) AS anual_expense,
                                                IFNULL(usa.type_expense, 0) AS type_expense,  
                                            -- Menú Administrador
                                                IFNULL(usa.user, 0) AS user, 
                                                IFNULL(usa.backup, 0) AS backup, 
                                                IFNULL(usa.general_cost_report, 0) AS general_cost_report, 
                                            -- Menú Cotizacion
                                                IFNULL(usa.quote_payment_method, 0) AS quote_payment_method, 
                                                IFNULL(usa.quote_company, 0) AS quote_company, 
                                                IFNULL(usa.quote_contact, 0) AS quote_contact, 
                                            -- Navegador Lista de Precios
                                                IFNULL(usa.price, 0) AS price, 
                                                IFNULL(usa.price_usd, 0) AS price_usd, 
                                                IFNULL(usa.type_custom_price, 0) AS type_custom_price,
                                            -- Navegador Herramientas
                                                IFNULL(usa.analysis_material, 0) AS analysis_material, 
                                                IFNULL(usa.economy_scale, 0) AS economy_scale,
                                                IFNULL(usa.sale_objectives, 0) AS sale_objectives,
                                                IFNULL(usa.price_objectives, 0) AS price_objectives,
                                                IFNULL(usa.multiproduct, 0) AS multiproduct,
                                                IFNULL(usa.historical, 0) AS historical,
                                                IFNULL(usa.simulator, 0) AS simulator, 
                                            -- Navegador Otros
                                                IFNULL(usa.support, 0) AS support, 
                                                IFNULL(usa.quote, 0) AS quote, 
                                                IFNULL(usa.contract, 0) AS contract
                                      FROM users us
                                        LEFT JOIN cost_users_access usa ON usa.id_user = us.id_user
                                      WHERE us.id_company = :id_company AND us.id_user = :id_user");
        $stmt->execute(['id_company' => $id_company, 'id_user' => $id_user]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
    }

    public function insertUserAccessByUser($dataUser, $typeCustomPrice)
    {
        $connection = Connection::getInstance()->getConnection();

        // price_usd,:price_usd, 'price_usd' => $dataUser['priceUSD'],

        try {
            $stmt = $connection->prepare("INSERT INTO cost_users_access 
                                            (
                                                id_user, 
                                                create_product, 
                                                create_materials, 
                                                create_machines, 
                                                create_process, 
                                                product_materials, 
                                                export_import, 
                                                external_service, 
                                                production_center, 
                                                anual_expense, 
                                                factory_load,
                                                payroll_load, 
                                                type_payroll, 
                                                expense, 
                                                expense_distribution, 
                                                type_expense, 
                                                user, 
                                                backup, 
                                                general_cost_report, 
                                                economy_scale, 
                                                sale_objectives, 
                                                price_objectives, 
                                                multiproduct,
                                                quote_payment_method, 
                                                quote_company, 
                                                quote_contact, 
                                                price, 
                                                custom_price, 
                                                type_custom_price, 
                                                analysis_material, 
                                                simulator, 
                                                historical, 
                                                support, 
                                                quote
                                            )
                                          VALUES 
                                            (
                                                :id_user, 
                                                :create_product, 
                                                :create_materials, 
                                                :create_machines, 
                                                :create_process, 
                                                :product_materials, 
                                                :export_import, 
                                                :external_service, 
                                                :production_center, 
                                                :anual_expense, 
                                                :factory_load, 
                                                :payroll_load, 
                                                :type_payroll, 
                                                :expense, 
                                                :expense_distribution, 
                                                :type_expense, 
                                                :user, 
                                                :backup, 
                                                :general_cost_report, 
                                                :economy_scale, 
                                                :sale_objectives, 
                                                :price_objectives,
                                                :multiproduct,
                                                :quote_payment_method, 
                                                :quote_company, 
                                                :quote_contact, 
                                                :price, 
                                                :custom_price, 
                                                :type_custom_price, 
                                                :analysis_material, 
                                                :simulator, 
                                                :historical, 
                                                :support, 
                                                :quote
                                            )");
            $stmt->execute([
                'id_user' => $dataUser['id_user'],
                'general_cost_report' => $dataUser['generalCostReport'],
                'create_product' => $dataUser['costCreateProducts'],
                'economy_scale' => $dataUser['economyScale'],
                'create_materials' => $dataUser['costCreateMaterials'],
                'sale_objectives' => $dataUser['saleObjectives'],
                'create_machines' => $dataUser['costCreateMachines'],
                'price_objectives' => $dataUser['priceObjectives'],
                'create_process' => $dataUser['costCreateProcess'],
                'multiproduct' => $dataUser['multiproduct'],
                'product_materials' => $dataUser['costProductMaterials'],
                'quote_payment_method' => $dataUser['quotePaymentMethod'],
                'export_import' => $dataUser['exportImport'],
                'quote_company' => $dataUser['quoteCompany'],
                'external_service' => $dataUser['externalService'],
                'quote_contact' => $dataUser['quoteContact'],
                'factory_load' => $dataUser['factoryLoad'],
                'price' => $dataUser['price'],
                'payroll_load' => $dataUser['payrollLoad'],
                'custom_price' => $dataUser['customPrices'],
                'expense' => $dataUser['expense'],
                'type_custom_price' => $typeCustomPrice,
                'expense_distribution' => $dataUser['expenseDistribution'],
                'analysis_material' => $dataUser['analysisMaterial'],
                'production_center' => $dataUser['production'],
                'simulator' => $dataUser['simulator'],
                'anual_expense' => $dataUser['anualExpense'],
                'historical' => $dataUser['historical'],
                'type_expense' => $dataUser['typeExpenses'],
                'support' => $dataUser['support'],
                'user' => $dataUser['costUser'],
                'quote' => $dataUser['quote'],
                'backup' => $dataUser['costBackup'],
                'type_payroll' => $dataUser['typePayroll'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function setDataUserAccessDemo($id_user)
    {
        $dataUser['id_user'] = $id_user;
        $dataUser['costCreateProducts'] = 1;
        $dataUser['costCreateMaterials'] = 1;
        $dataUser['exportImport'] = 1;
        $dataUser['costCreateMachines'] = 1;
        $dataUser['costCreateProcess'] = 1;
        $dataUser['costProductMaterials'] = 1;
        $dataUser['factoryLoad'] = 1;
        $dataUser['externalService'] = 1;
        $dataUser['payrollLoad'] = 1;
        $dataUser['costUser'] = 1;
        $dataUser['costBackup'] = 1;
        $dataUser['quotePaymentMethod'] = 1;
        $dataUser['quoteCompany'] = 1;
        $dataUser['quoteContact'] = 1;
        $dataUser['price'] = 1;
        $dataUser['customPrices'] = 1;
        $dataUser['analysisMaterial'] = 1;
        $dataUser['economyScale'] = 1;
        $dataUser['saleObjectives'] = 1;
        $dataUser['multiproduct'] = 1;
        $dataUser['simulator'] = 1;
        $dataUser['historical'] = 1;
        $dataUser['generalCostReport'] = 1;
        $dataUser['quote'] = 1;
        $dataUser['support'] = 1;
        $dataUser['expense'] = 1;
        $dataUser['expenseDistribution'] = 1;
        $dataUser['production'] = 1;
        $dataUser['anualExpense'] = 1;
        $dataUser['typeCustomPrices'] = -1;
        $dataUser['typePayroll'] = 1;
        $dataUser['typeExpenses'] = 1;
        $dataUser['priceObjectives'] = 1;
        $dataUser['priceUSD'] = 1;

        return $dataUser;
    }

    public function updateUserAccessByUsers($dataUser, $typeCustomPrice)
    {
        $connection = Connection::getInstance()->getConnection();
        /* Hacer un select
            Contar los usuarios
            si el usuario es > 1 no hacer nada
            de lo contrario realizar la actualizacion
            price_usd = :price_usd,
            'price_usd' => $dataUser['priceUSD'],
         */
        $stmt = $connection->prepare("SELECT * FROM cost_users_access");
        $stmt->execute();
        $rows = $stmt->rowCount();

        if ($rows > 1) {

            try {
                $stmt = $connection->prepare("UPDATE cost_users_access SET create_product = :create_product, create_materials = :create_materials, create_machines = :create_machines, create_process = :create_process, product_materials = :product_materials, export_import = :export_import, external_service = :external_service, type_payroll = :type_payroll, production_center = :production_center, anual_expense = :anual_expense,
                                                                           factory_load = :factory_load, payroll_load = :payroll_load, expense = :expense, expense_distribution = :expense_distribution, type_expense = :type_expense, user = :user, backup = :backup, general_cost_report = :general_cost_report, economy_scale = :economy_scale, sale_objectives = :sale_objectives, price_objectives = :price_objectives, multiproduct = :multiproduct, 
                                                                           price = :price, custom_price = :custom_price, type_custom_price = :type_custom_price, analysis_material = :analysis_material, simulator = :simulator, historical = :historical, support = :support, quote = :quote, quote_payment_method = :quote_payment_method, quote_company = :quote_company, quote_contact = :quote_contact
                                              WHERE id_user = :id_user");
                $stmt->execute([
                    'id_user' => $dataUser['id_user'],
                    'general_cost_report' => $dataUser['generalCostReport'],
                    'create_product' => $dataUser['costCreateProducts'],
                    'economy_scale' => $dataUser['economyScale'],
                    'create_materials' => $dataUser['costCreateMaterials'],
                    'sale_objectives' => $dataUser['saleObjectives'],
                    'create_machines' => $dataUser['costCreateMachines'],
                    'price_objectives' => $dataUser['priceObjectives'],
                    'create_process' => $dataUser['costCreateProcess'],
                    'multiproduct' => $dataUser['multiproduct'],
                    'product_materials' => $dataUser['costProductMaterials'],
                    'quote_payment_method' => $dataUser['quotePaymentMethod'],
                    'export_import' => $dataUser['exportImport'],
                    'quote_company' => $dataUser['quoteCompany'],
                    'external_service' => $dataUser['externalService'],
                    'quote_contact' => $dataUser['quoteContact'],
                    'factory_load' => $dataUser['factoryLoad'],
                    'price' => $dataUser['price'],
                    'payroll_load' => $dataUser['payrollLoad'],
                    'custom_price' => $dataUser['customPrices'],
                    'expense' => $dataUser['expense'],
                    'type_custom_price' => $typeCustomPrice,
                    'expense_distribution' => $dataUser['expenseDistribution'],
                    'analysis_material' => $dataUser['analysisMaterial'],
                    'production_center' => $dataUser['production'],
                    'simulator' => $dataUser['simulator'],
                    'anual_expense' => $dataUser['anualExpense'],
                    'historical' => $dataUser['historical'],
                    'type_expense' => $dataUser['typeExpenses'],
                    'support' => $dataUser['support'],
                    'user' => $dataUser['costUser'],
                    'quote' => $dataUser['quote'],
                    'backup' => $dataUser['costBackup'],
                    'type_payroll' => $dataUser['typePayroll'],
                ]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $error = array('error' => true, 'message' => $message);
                return $error;
            }
        } else {
            return 1;
        }
    }

    public function deleteUserAccess($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("DELETE FROM cost_users_access WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $dataUser['id_user']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
}
