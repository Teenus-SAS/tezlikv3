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
        $stmt = $connection->prepare("SELECT us.id_user, us.firstname, us.lastname, us.email, IFNULL(usa.create_product, 0) AS create_product, IFNULL(usa.create_materials, 0) AS create_materials, IFNULL(usa.create_machines, 0) AS create_machines, IFNULL(usa.create_process, 0) AS create_process, IFNULL(usa.product_materials, 0) AS product_materials, 
                                                 IFNULL(usa.product_process, 0) AS product_process, IFNULL(usa.factory_load, 0) AS factory_load, IFNULL(usa.external_service, 0) AS external_service, IFNULL(usa.payroll_load, 0) AS payroll_load, IFNULL(usa.expense, 0) AS expense, IFNULL(usa.expense_distribution, 0) AS expense_distribution, 
                                                 IFNULL(usa.user, 0) AS user, IFNULL(usa.backup, 0) AS backup, IFNULL(usa.economy_scale, 0) AS economy_scale, IFNULL(usa.multiproduct, 0) AS multiproduct, IFNULL(usa.quote_payment_method, 0) AS quote_payment_method, IFNULL(usa.quote_company, 0) AS quote_company, IFNULL(usa.quote_contact, 0) AS quote_contact, 
                                                 IFNULL(usa.price, 0) AS price, IFNULL(usa.price_usd, 0) AS price_usd, IFNULL(usa.analysis_material, 0) AS analysis_material, IFNULL(usa.simulator, 0) AS simulator, IFNULL(usa.support, 0) AS support, IFNULL(usa.quote, 0) AS quote
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
        $stmt = $connection->prepare(
            "SELECT us.id_user, us.firstname, us.lastname, us.email, IFNULL(usa.create_product, 0) AS create_product, IFNULL(usa.create_materials, 0) AS create_materials, IFNULL(usa.create_machines, 0) AS create_machines, IFNULL(usa.create_process, 0) AS create_process, IFNULL(usa.product_materials, 0) AS product_materials, 
                    IFNULL(usa.product_process, 0) AS product_process, IFNULL(usa.factory_load, 0) AS factory_load, IFNULL(usa.external_service, 0) AS external_service, IFNULL(usa.payroll_load, 0) AS payroll_load, IFNULL(usa.expense, 0) AS expense, IFNULL(usa.expense_distribution, 0) AS expense_distribution, 
                    IFNULL(usa.user, 0) AS user, IFNULL(usa.economy_scale, 0) AS economy_scale, IFNULL(usa.multiproduct, 0) AS multiproduct, IFNULL(usa.quote_payment_method, 0) AS quote_payment_method, IFNULL(usa.quote_company, 0) AS quote_company, IFNULL(usa.quote_contact, 0) AS quote_contact, 
                    IFNULL(usa.price, 0) AS price, IFNULL(usa.price_usd, 0) AS price_usd, IFNULL(usa.analysis_material, 0) AS analysis_material, IFNULL(usa.simulator, 0) AS simulator, IFNULL(usa.support, 0) AS support, IFNULL(usa.quote, 0) AS quote
             FROM users us
             LEFT JOIN cost_users_access usa ON usa.id_user = us.id_user
             WHERE us.id_company = :id_company AND us.id_user = :id_user"
        );
        $stmt->execute(['id_company' => $id_company, 'id_user' => $id_user]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
    }

    public function insertUserAccessByUser($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO cost_users_access (id_user, create_product, create_materials, create_machines, create_process, product_materials, product_process, 
                                                                         factory_load, external_service, payroll_load, expense, expense_distribution, user, backup, economy_scale, multiproduct,
                                                                         quote_payment_method, quote_company, quote_contact, price, price_usd, analysis_material, simulator, support, quote)
                                          VALUES (:id_user, :create_product, :create_materials, :create_machines, :create_process, :product_materials, :product_process, 
                                                  :factory_load, :external_service, :payroll_load, :expense, :expense_distribution, :user, :backup, :economy_scale, :multiproduct,
                                                  :quote_payment_method, :quote_company, :quote_contact, :price, :price_usd, :analysis_material, :simulator, :support, :quote)");
            $stmt->execute([
                'id_user' => $dataUser['idUser'],                               'backup' => $dataUser['costBackup'],
                'create_product' => $dataUser['costCreateProducts'],            'economy_scale' => $dataUser['economyScale'],
                'create_materials' => $dataUser['costCreateMaterials'],         'multiproduct' => $dataUser['multiproduct'],
                'create_machines' => $dataUser['costCreateMachines'],           'quote_payment_method' => $dataUser['quotePaymentMethod'],
                'create_process' => $dataUser['costCreateProcess'],             'quote_company' => $dataUser['quoteCompany'],
                'product_materials' => $dataUser['costProductMaterials'],       'quote_contact' => $dataUser['quoteContact'],
                'product_process' => $dataUser['costProductProcess'],           'price' => $dataUser['price'],
                'factory_load' => $dataUser['factoryLoad'],                     'price_usd' => $dataUser['priceUSD'],
                'external_service' => $dataUser['externalService'],             'analysis_material' => $dataUser['analysisMaterial'],
                'payroll_load' => $dataUser['payrollLoad'],                     'simulator' => $dataUser['simulator'],
                'expense' => $dataUser['expense'],                              'support' => $dataUser['support'],
                'expense_distribution' => $dataUser['expenseDistribution'],     'quote' => $dataUser['quote'],
                'user' => $dataUser['costUser'],     'quote' => $dataUser['quote'],
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
        $dataUser['idUser'] = $id_user;
        $dataUser['costUser'] = 1;
        $dataUser['costBackup'] = 1;
        $dataUser['costCreateProducts'] = 1;
        $dataUser['economyScale'] = 1;
        $dataUser['costCreateMaterials'] = 1;
        $dataUser['multiproduct'] = 1;
        $dataUser['simulator'] = 1;
        $dataUser['costCreateMachines'] = 1;
        $dataUser['quotePaymentMethod'] = 1;
        $dataUser['costCreateProcess'] = 1;
        $dataUser['quoteCompany'] = 1;
        $dataUser['costProductMaterials'] = 1;
        $dataUser['quoteContact'] = 1;
        $dataUser['costProductProcess'] = 1;
        $dataUser['price'] = 1;
        $dataUser['factoryLoad'] = 1;
        $dataUser['priceUSD'] = 1;
        $dataUser['externalService'] = 1;
        $dataUser['analysisMaterial'] = 1;
        $dataUser['payrollLoad'] = 1;
        $dataUser['support'] = 1;
        $dataUser['expense'] = 1;
        $dataUser['quote'] = 1;
        $dataUser['expenseDistribution'] = 1;

        return $dataUser;
    }

    public function updateUserAccessByUsers($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();
        /* Hacer un select
            Contar los usuarios
            si el usuario es > 1 no hacer nada
            de lo contrario realizar la actualizacion
         */
        $stmt = $connection->prepare("SELECT * FROM cost_users_access");
        $stmt->execute();
        $rows = $stmt->rowCount();

        if ($rows > 1) {

            try {
                $stmt = $connection->prepare("UPDATE cost_users_access SET create_product = :create_product, create_materials = :create_materials, create_machines = :create_machines, create_process = :create_process, product_materials = :product_materials, product_process = :product_process, 
                                                                           factory_load = :factory_load, external_service = :external_service, payroll_load = :payroll_load, expense = :expense, expense_distribution = :expense_distribution, user = :user, backup = :backup, economy_scale = :economy_scale, multiproduct = :multiproduct, 
                                                                           price = :price, price_usd = :price_usd, analysis_material = :analysis_material, simulator = :simulator, support = :support, quote = :quote, quote_payment_method = :quote_payment_method, quote_company = :quote_company, quote_contact = :quote_contact
                                              WHERE id_user = :id_user");
                $stmt->execute([
                    'id_user' => $dataUser['idUser'],                               'backup' => $dataUser['costBackup'],
                    'create_product' => $dataUser['costCreateProducts'],            'economy_scale' => $dataUser['economyScale'],
                    'create_materials' => $dataUser['costCreateMaterials'],         'multiproduct' => $dataUser['multiproduct'],
                    'create_machines' => $dataUser['costCreateMachines'],           'quote_payment_method' => $dataUser['quotePaymentMethod'],
                    'create_process' => $dataUser['costCreateProcess'],             'quote_company' => $dataUser['quoteCompany'],
                    'product_materials' => $dataUser['costProductMaterials'],       'quote_contact' => $dataUser['quoteContact'],
                    'product_process' => $dataUser['costProductProcess'],           'price' => $dataUser['price'],
                    'factory_load' => $dataUser['factoryLoad'],                     'price_usd' => $dataUser['priceUSD'],
                    'external_service' => $dataUser['externalService'],             'analysis_material' => $dataUser['analysisMaterial'],
                    'payroll_load' => $dataUser['payrollLoad'],                     'simulator' => $dataUser['simulator'],
                    'expense' => $dataUser['expense'],                              'support' => $dataUser['support'],
                    'expense_distribution' => $dataUser['expenseDistribution'],     'quote' => $dataUser['quote'],
                    'user' => $dataUser['costUser'],     'quote' => $dataUser['quote'],
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
        $stmt->execute(['id_user' => $dataUser['idUser']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
}
