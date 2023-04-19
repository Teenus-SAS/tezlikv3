<?php

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
                $first_date = $arr['vigenciadesde'];
                $last_date = $arr['vigenciahasta'];

                for ($date = $first_date; $date <= $last_date;) {
                    $resp = $trmDao->insertTrm($date, $arr['valor']);

                    if (isset($resp['info'])) break;

                    $date = date('Y-m-d', strtotime($date . ' +1 day'));
                }
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
