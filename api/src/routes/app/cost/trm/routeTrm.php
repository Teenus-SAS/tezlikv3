<?php

use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();

// Modificar TRM historico Diario 
function UpdateLastTrm($trmDao)
{
    $date = date('Y-m-d');
    // Obtener trm actual
    $price = $trmDao->getActualTrm($date);

    // Insertar
    $resolution = $trmDao->insertActualTrm($date, $price);

    // Eliminar primer registro del historico
    if ($resolution == null)
        $resolution = $trmDao->deleteFirstTrm();
}

date_default_timezone_set('America/Bogota');
$hour = date('H:i');
if ($hour == '00:01')
    UpdateLastTrm($trmDao);
