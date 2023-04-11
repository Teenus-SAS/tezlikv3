<?php

use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/historicalTrm', function (Request $request, Response $response, $args) use ($trmDao) {
    $trm = $trmDao->findAllHistoricalTrm();
    $response->getBody()->write(json_encode($trm));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/loadLastsTrm', function (Request $request, Response $response, $args) use ($trmDao) {
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

    if (isset($price['info']))
        $resp = array('info' => true, 'message' => 'Error al ingresar al historico de trm. Intente nuevamente');
    else
        $resp = array('success' => true, 'message' => 'Trm cargado correctamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
