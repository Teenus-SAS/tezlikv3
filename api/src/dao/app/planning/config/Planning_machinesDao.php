<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Planning_machinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllPlanMachines($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pm.id_program_machine, m.machine, pm.number_workers, pm.hours_day, pm.hour_start, pm.hour_end, pm.year, pm.january, pm.february,
                                             pm.march, pm.april, pm.may, pm.june, pm.july, pm.august, pm.september, pm.october, pm.november, pm.december                                
                                      FROM plan_program_machines pm
                                        INNER JOIN machines m ON m.id_machine = pm.id_machine
                                      WHERE pm.id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $planningMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $planningMachines;
    }

    /* Buscar si existe en la base de datos */
    public function findPlanMachines($dataPMachines, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_program_machines 
                                      WHERE id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute(['id_machine' => $dataPMachines['idMachine'], 'id_company' => $id_company]);
        $planningMachines = $stmt->fetch($connection::FETCH_ASSOC);
        return $planningMachines;
    }

    public function insertPlanMachinesByCompany($dataPMachines, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $time = $this->timeConverter($dataPMachines);

        try {
            $stmt = $connection->prepare("INSERT INTO plan_program_machines (id_machine, id_company, number_workers, hours_day, hour_start, hour_end, year, january, 
                                                        february, march, april, may, june, july, august, september, october, november, december)
                                      VALUES (:id_machine, :id_company, :number_workers, :hours_day, :hour_start, :hour_end, :year, :january, 
                                              :february, :march, :april, :may, :june, :july, :august, :september, :october, :november, :december)");
            $stmt->execute([
                'id_company' => $id_company,                            'april' => $time['april'],
                'id_machine' => $dataPMachines['idMachine'],            'may' => $time['may'],
                'number_workers' => $dataPMachines['numberWorkers'],    'june' => $time['june'],
                'hours_day' => $dataPMachines['hoursDay'],              'july' => $time['july'],
                'hour_start' => $time['hourStart'],                     'august' => $time['august'],
                'hour_end' => $time['hourEnd'],                         'september' => $time['september'],
                'year' =>  $time['year'],                               'october' => $time['october'],
                'january' => $time['january'],                          'november' => $time['november'],
                'february' => $time['february'],                        'december' => $time['december'],
                'march' => $time['march']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePlanMachines($dataPMachines)
    {
        $connection = Connection::getInstance()->getConnection();

        $time = $this->timeConverter($dataPMachines);

        try {
            $stmt = $connection->prepare("UPDATE plan_program_machines SET id_machine = :id_machine, number_workers = :number_workers, hours_day = :hours_day, hour_start = :hour_start, hour_end = :hour_end, 
                                                    year = :year, january = :january, february = :february, march = :march, april = :april, may = :may, june = :june, july = :july,
                                                    august = :august, september = :september, october = :october, november = :november, december = :december
                                          WHERE id_program_machine = :id_program_machine");
            $stmt->execute([
                'id_program_machine' => $dataPMachines['idProgramMachine'],     'april' => $time['april'],
                'id_machine' => $dataPMachines['idMachine'],                    'may' => $time['may'],
                'number_workers' => $dataPMachines['numberWorkers'],            'june' => $time['june'],
                'hours_day' => $dataPMachines['hoursDay'],                      'july' => $time['july'],
                'hour_start' => $time['hourStart'],                             'august' => $time['august'],
                'hour_end' => $time['hourEnd'],                                 'september' => $time['september'],
                'year' => $time['year'],                                        'october' => $time['october'],
                'january' => $time['january'],                                  'november' => $time['november'],
                'february' => $time['february'],                                'december' => $time['december'],
                'march' => $time['march']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deletePlanMachines($id_program_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_program_machines WHERE id_program_machine = :id_program_machine");
        $stmt->execute(['id_program_machine' => $id_program_machine]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM plan_program_machines WHERE id_program_machine = :id_program_machine");
            $stmt->execute(['id_program_machine' => $id_program_machine]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }

    // Convertir tiempo
    public function timeConverter($dataPMachines)
    {
        $time['year'] = date('Y');
        $time['hourStart'] = date("G:i", strtotime($dataPMachines['hourStart']));
        $time['hourEnd'] = date("G:i", strtotime($dataPMachines['hourEnd']));
        $time['january'] = date("Y-m-d", strtotime($dataPMachines['january']));
        $time['february'] = date("Y-m-d", strtotime($dataPMachines['february']));
        $time['march'] = date("Y-m-d", strtotime($dataPMachines['march']));
        $time['april'] = date("Y-m-d", strtotime($dataPMachines['april']));
        $time['may'] = date("Y-m-d", strtotime($dataPMachines['may']));
        $time['june'] = date("Y-m-d", strtotime($dataPMachines['june']));
        $time['july'] = date("Y-m-d", strtotime($dataPMachines['july']));
        $time['august'] = date("Y-m-d", strtotime($dataPMachines['august']));
        $time['september'] = date("Y-m-d", strtotime($dataPMachines['september']));
        $time['october'] = date("Y-m-d", strtotime($dataPMachines['october']));
        $time['november'] = date("Y-m-d", strtotime($dataPMachines['november']));
        $time['december'] = date("Y-m-d", strtotime($dataPMachines['december']));

        return $time;
    }
}
