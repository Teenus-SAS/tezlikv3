<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PlanningUserAccessDao
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
            $stmt = $connection->prepare("SELECT usa.id_planning_user_access, usa.id_user, us.firstname, us.lastname, us.email, usa.create_mold, usa.create_product, usa.create_material, 
                                                 usa.create_machine, usa.create_process, usa.products_material, usa.products_process, usa.programs_machine, usa.cicles_machine, usa.inv_category, 
                                                 usa.sale, usa.user, usa.inventory, usa.plan_order, usa.programming, usa.plan_load, usa.explosion_of_material, usa.office
                                          FROM planning_user_access usa 
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
            "SELECT usa.id_planning_user_access, usa.id_user, us.firstname, us.lastname, us.email, usa.create_mold, usa.create_product, usa.create_material, 
                    usa.create_machine, usa.create_process, usa.products_material, usa.products_process, usa.programs_machine, usa.cicles_machine, usa.inv_category, 
                    usa.sale, usa.user, usa.inventory, usa.plan_order, usa.programming, usa.plan_load, usa.explosion_of_material, usa.office
             FROM planning_user_access usa 
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
            $stmt = $connection->prepare("INSERT INTO planning_user_access (id_user, create_mold, create_product, create_material, create_machine, create_process, products_material, products_process, programs_machine, 
                                                                            cicles_machine, inv_category, sale, user, inventory, plan_order, programming, plan_load, explosion_of_material, office) 
                                          VALUES (:id_user, :create_mold, :create_product, :create_material, :create_machine, :create_process, :products_material, :products_process, 
                                                    :programs_machine, :cicles_machine, :inv_category, :sale, :user, :inventory, :plan_order, :programming, :plan_load, :explosion_of_material, :office)");
            $stmt->execute([
                'id_user' => $idUser['idUser'],                                 'inv_category' => $dataUser['invCategory'],
                'create_mold' => $dataUser['createMold'],                       'sale' => $dataUser['sale'],
                'create_product' => $dataUser['createProduct'],                 'user' => $dataUser['user'],
                'create_material' => $dataUser['createMaterial'],               'inventory' => $dataUser['inventory'],
                'create_machine' => $dataUser['createMachine'],                 'plan_order' => $dataUser['order'],
                'create_process' => $dataUser['createProcess'],                 'programming' => $dataUser['programming'],
                'products_material' => $dataUser['productsMaterial'],           'plan_load' => $dataUser['load'],
                'products_process' => $dataUser['productsProcess'],             'explosion_of_material' => $dataUser['explosionOfMaterial'],
                'programs_machine' => $dataUser['programsMachine'],             'office' => $dataUser['office'],
                'cicles_machine' => $dataUser['ciclesMachine']
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
                $stmt = $connection->prepare("UPDATE planning_user_access SET create_mold = :create_mold, create_product = :create_product, create_material = :create_material, create_machine = :create_machine, create_process = :create_process, products_material = :products_material, 
                                                                            products_process = :products_process, programs_machine = :programs_machine, cicles_machine = :cicles_machine, inv_category = :inv_category, sale = :sale, 
                                                                            user = :user, inventory = :inventory, plan_order = :plan_order, programming = :programming, plan_load = :plan_load, explosion_of_material = :explosion_of_material, office = :office
                                              WHERE id_planning_user_access = :id_planning_user_access");
                $stmt->execute([
                    'id_planning_user_access' => $dataUser['idUserAccess'],         'inv_category' => $dataUser['invCategory'],
                    'create_mold' => $dataUser['createMold'],                       'sale' => $dataUser['sale'],
                    'create_product' => $dataUser['createProduct'],                 'user' => $dataUser['user'],
                    'create_material' => $dataUser['createMaterial'],               'inventory' => $dataUser['inventory'],
                    'create_machine' => $dataUser['createMachine'],                 'plan_order' => $dataUser['order'],
                    'create_process' => $dataUser['createProcess'],                 'programming' => $dataUser['programming'],
                    'products_material' => $dataUser['productsMaterial'],           'plan_load' => $dataUser['load'],
                    'products_process' => $dataUser['productsProcess'],             'explosion_of_material' => $dataUser['explosionOfMaterial'],
                    'programs_machine' => $dataUser['programsMachine'],             'office' => $dataUser['office'],
                    'cicles_machine' => $dataUser['ciclesMachine']
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
        $stmt = $connection->prepare("DELETE FROM planning_user_access WHERE id_planning_user_access = :id_planning_user_access");
        $stmt->execute(['id_planning_user_access' => $dataUser['idUserAccess']]);
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
