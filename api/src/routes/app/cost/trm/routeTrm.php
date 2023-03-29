<?php

use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();

// Modificar TRM historico Diario 
function UpdateLastTrm($trmDao)
{
    $date = date('Y-m-d');

    $lastTrm = $trmDao->findLastInsertedTrm();
    while ($lastTrm['date_trm'] < $date) {
        if ($lastTrm['date_trm'] < $date) {
            $lastTrm = $trmDao->findLastInsertedTrm();

            $date_trm = date('Y-m-d', strtotime($lastTrm['date_trm'] . ' +1 day'));

            // Obtener trm
            $price = $trmDao->getTrm($date_trm);

            // Insertar
            $resolution = $trmDao->insertTrm($date_trm, $price);

            // Eliminar primer registro del historico
            if ($resolution == null)
                $resolution = $trmDao->deleteFirstTrm();
        } else break;
    }
}

date_default_timezone_set('America/Bogota');
$today = date("H:i");


if ($today == '00:00')
    UpdateLastTrm($trmDao);
