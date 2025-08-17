<?php

use tezlikv3\dao\CompaniesDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\MachinesDao;
use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();
$machinesDao = new MachinesDao();
$IndirectCostDao = new IndirectCostDao();
$CostWorkforceDao = new CostWorkforceDao();
$CompaniesDao = new CompaniesDao();

// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;

// Modificar TRM historico Diario 
function updateLastTrm($trmDao, $today)
{
    try {
        $historicalTrm = $trmDao->getAllHistoricalTrm();

        if ($historicalTrm == 1) {
            return ['error' => true, 'message' => 'Error al guardar la información. Intente mas tarde'];
        }

        if (is_array($historicalTrm)) {
            $resp = $trmDao->deleteAllHistoricalTrm();

            if ($resp == null) {
                $historicalTrm = $trmDao->getAllHistoricalTrm();

                if ($historicalTrm == 1) {
                    return ['error' => true, 'message' => 'Error al guardar la información. Intente mas tarde'];
                }

                $status = true;

                foreach ($historicalTrm as $arr) {
                    if ($status == false) break;

                    $first_date = $arr['vigenciadesde'];
                    $last_date = $arr['vigenciahasta'];

                    for ($date = $first_date; $date <= $last_date;) {
                        if ($status == false) break;

                        $trm_date = date('Y-m-d', strtotime($date . ' +2 years'));

                        if ($trm_date == $today) $status = false;

                        $resp = $trmDao->insertTrm($date, $arr['valor']);

                        if (isset($resp['info'])) break;

                        $date = date('Y-m-d', strtotime($date . ' +1 day'));
                    }
                }

                $trmDao->deleteTrm();
            }
        }
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}
$date = date('Y-m-d');

$lastTrm = $trmDao->findLastInsertedTrm($date);

!is_array($lastTrm) || isset($lastTrm['info']) ? $data['date_trm'] = date('Y-m-d', strtotime($date . ' -1 day')) : $data = $lastTrm;

if ($date > $data['date_trm'])
    updateLastTrm($trmDao, $date);
