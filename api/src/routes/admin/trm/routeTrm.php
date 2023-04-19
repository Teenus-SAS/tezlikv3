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

    if (isset($price['info']))
        $resp = array('info' => true, 'message' => $price['message']);
    else
        $resp = array('success' => true, 'message' => 'Trm cargado correctamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
