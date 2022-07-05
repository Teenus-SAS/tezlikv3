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
        $rol = $_SESSION['rol'];

        if ($rol == 2) {
            $stmt = $connection->prepare("SELECT usa.id_cost_user_access, usa.id_user, us.firstname, us.lastname, us.email, usa.create_product, usa.create_materials, usa.create_machines,
                                             usa.create_process, usa.product_materials, usa.product_process, usa.factory_load, usa.external_service, 
                                                    usa.product_line, usa.payroll_load, usa.expense, usa.expense_distribution, usa.user  
                                      FROM cost_users_access usa 
                                      INNER JOIN users us ON us.id_user = usa.id_user
                                      WHERE us.id_company = :id_company;");
            $stmt->execute(['id_company' => $id_company]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            $users = $stmt->fetchAll($connection::FETCH_ASSOC);
            $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
            return $users;
        }
    }

    // public function findUserAccess($id_company, $id_user)
    // {
    //     $connection = Connection::getInstance()->getConnection();
    //     $rol = $_SESSION['rol'];

    //     if ($rol == 2) {
    //         $stmt = $connection->prepare("SELECT usa.create_product, usa.create_materials, usa.create_machines, usa.create_process, usa.product_materials, usa.product_process  
    //                                   FROM cost_users_access usa 
    //                                   INNER JOIN users us ON us.id_user = usa.id_user
    //                                   WHERE us.id_company = :id_company AND us.id_user = :id_user;");
    //         $stmt->execute(['id_company' => $id_company, 'id_user' => $id_user]);
    //         $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //         $users = $stmt->fetchAll($connection::FETCH_ASSOC);
    //         $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
    //         return $users;
    //     }
    // }

    public function findUserAccess($id_company, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare(
            "SELECT usa.create_product, usa.create_materials, usa.create_machines, usa.create_process, usa.product_materials,
                    usa.product_process, usa.factory_load, usa.external_service, usa.product_line, usa.payroll_load, usa.expense,
                    usa.expense_distribution, usa.user
             FROM cost_users_access usa 
             INNER JOIN users us ON us.id_user = usa.id_user
             WHERE us.id_company = :id_company AND us.id_user = :id_user;"
        );
        $stmt->execute(['id_company' => $id_company, 'id_user' => $id_user]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
    }

    public function insertUserAccessByUser($dataUser)
    {
        //session_start();
        $id_company = $_SESSION['id_company'];
        $connection = Connection::getInstance()->getConnection();

        /* Obtener id usuario creado */

        $stmt = $connection->prepare("SELECT MAX(id_user) AS idUser FROM users WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $idUser = $stmt->fetch($connection::FETCH_ASSOC);

        try {
            $stmt = $connection->prepare("INSERT INTO cost_users_access (id_user, create_product, create_materials, create_machines, create_process,
                                                            product_materials, product_process, factory_load, external_service,
                                                            product_line, payroll_load, expense, expense_distribution, user)
                                          VALUES (:id_user, :create_product, :create_materials, :create_machines, :create_process,
                                                :product_materials, :product_process, :factory_load, :external_service,
                                                :product_line, :payroll_load, :expense, :expense_distribution, :user)");
            $stmt->execute([
                'id_user' => $idUser['idUser'],                                        'factory_load' => $dataUser['factoryLoad'],
                'create_product' => $dataUser['createProducts'],           'external_service' => $dataUser['externalService'],
                'create_materials' => $dataUser['createMaterials'],       'product_line' => $dataUser['productLine'],
                'create_machines' => $dataUser['createMachines'],         'payroll_load' => $dataUser['payrollLoad'],
                'create_process' => $dataUser['createProcess'],           'expense' => $dataUser['expense'],
                'product_materials' => $dataUser['productMaterials'],     'expense_distribution' => $dataUser['expenseDistribution'],
                'product_process' => $dataUser['productProcess'],         'user' => $dataUser['user']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
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
                $stmt = $connection->prepare("UPDATE cost_users_access SET create_product = :create_product, create_materials = :create_materials, create_machines = :create_machines, create_process = :create_process, 
                                                            product_materials = :product_materials, product_process = :product_process, factory_load = :factory_load, external_service = :external_service,
                                                            product_line = :product_line, payroll_load = :payroll_load, expense = :expense, expense_distribution = :expense_distribution, user = :user
                                              WHERE id_cost_user_access = :id_cost_user_access");
                $stmt->execute([
                    'id_cost_user_access' => $dataUser['idUser'],                                          'factory_load' => $dataUser['factoryLoad'],
                    'create_product' => $dataUser['createProducts'],           'external_service' => $dataUser['externalService'],
                    'create_materials' => $dataUser['createMaterials'],       'product_line' => $dataUser['productLine'],
                    'create_machines' => $dataUser['createMachines'],         'payroll_load' => $dataUser['payrollLoad'],
                    'create_process' => $dataUser['createProcess'],           'expense' => $dataUser['expense'],
                    'product_materials' => $dataUser['productMaterials'],     'expense_distribution' => $dataUser['expenseDistribution'],
                    'product_process' => $dataUser['productProcess'],         'user' => $dataUser['user']
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
        $stmt = $connection->prepare("DELETE FROM cost_users_access WHERE id_cost_user_access = :id_cost_user_access");
        $stmt->execute(['id_cost_user_access' => $dataUser['idUserAccess']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));



        // session_start();
        // $idUser = $_SESSION['idUser'];

        // $connection = Connection::getInstance()->getConnection();

        // if ($dataUser['idUser'] != $idUser) {

        //     $stmt = $connection->prepare("SELECT * FROM cost_users_access WHERE id_cost_user_access = :id_cost_user_access");
        //     $stmt->execute(['id_cost_user_access' => $dataUser['idUserAccess']]);
        //     $rows = $stmt->rowCount();

        //     if ($rows > 0) {
        //         $stmt = $connection->prepare("DELETE FROM cost_users_access WHERE id_cost_user_access = :id_cost_user_access");
        //         $stmt->execute(['id_cost_user_access' => $dataUser['idUserAccess']]);
        //         $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        //     }
        // } else {
        //     return 1;
        // }
    }
}
