<?php

use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();

// Modificar TRM historico Diario 
function updateLastTrm($trmDao)
{
    $date = date('Y-m-d');

    $lastTrm = $trmDao->findLastInsertedTrm();
    $dateTrm = $lastTrm['date_trm'];

    for ($dateTrm; $dateTrm <= $date;) {
        $lastTrm = $trmDao->findLastInsertedTrm();

        $dateTrm = date('Y-m-d', strtotime($lastTrm['date_trm'] . ' +1 day'));
        if ($dateTrm > $date) break;

        // Obtener trm
        $price = $trmDao->getTrm($dateTrm);

        if (isset($price['info'])) break;

        // Insertar
        $resolution = $trmDao->insertTrm($dateTrm, $price);

        // Eliminar primer registro del historico
        if ($resolution == null)
            $resolution = $trmDao->deleteFirstTrm();
    }
}

date_default_timezone_set('America/Bogota');
$today = date("H:i");

if ($today >= '20:00')
    updateLastTrm($trmDao);
