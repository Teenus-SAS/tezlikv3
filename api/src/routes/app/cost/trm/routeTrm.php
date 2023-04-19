<?php
/*
use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();

// Modificar TRM historico Diario 
function updateLastTrm($trmDao)
{
    try {

        $resp = $trmDao->deleteAllHistoricalTrm();

        if ($resp == null) {
            $historicalTrm = $trmDao->getAllHistoricalTrm();

            foreach ($historicalTrm as $arr) {
                $date = $arr['vigenciadesde'];
                $value = $arr['valor'];
                $resp = $trmDao->insertTrm($date, $value);

                if (isset($resp['info'])) break;
            }
        }
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}
$date = date('Y-m-d');

$lastTrm = $trmDao->findLastInsertedTrm();

if ($date > $lastTrm['date_trm'])
    updateLastTrm($trmDao);
*/