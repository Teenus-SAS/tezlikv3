<?php

use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();

// Modificar TRM historico Diario 
function updateLastTrm($trmDao)
{
    $date = date('Y-m-d');

    $lastTrm = $trmDao->findLastInsertedTrm();

    $dateTrm = $lastTrm['date_trm'];

    while ($dateTrm < $date) {
        $lastTrm = $trmDao->findLastInsertedTrm();
        $dateTrm = $lastTrm['date_trm'];

        $date_trm = date('Y-m-d', strtotime($lastTrm['date_trm'] . ' +1 day'));

        if ($date_trm > $date) break;

        // Obtener trm
        $price = $trmDao->getTrm($date_trm);

        // Insertar
        $resolution = $trmDao->insertTrm($date_trm, $price);

        // Eliminar primer registro del historico
        if ($resolution == null)
            $resolution = $trmDao->deleteFirstTrm();
    }
}

date_default_timezone_set('America/Bogota');
$today = date("H:i");

if ($today >= '19:00')
    updateLastTrm($trmDao);
