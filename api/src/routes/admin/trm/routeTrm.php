<?php

use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/historicalTrm', function (RouteCollectorProxy $group) use ($trmDao) {

    $group->get('', function (Request $request, Response $response, $args) use ($trmDao) {
        $trm = $trmDao->findAllHistoricalTrm();
        $response->getBody()->write(json_encode($trm));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/loadLastsTrm', function (Request $request, Response $response, $args) use ($trmDao) {
        $resp = $trmDao->deleteAllHistoricalTrm();
        $today = date('Y-m-d');

        if ($resp == null) {
            $historicalTrm = $trmDao->getAllHistoricalTrm();

            if ($historicalTrm == 1) {
                $resp = ['error' => true, 'message' => 'Error al cargar la información. Intente mas tarde'];

                $response->getBody()->write(json_encode($resp));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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

        if (isset($price['info']))
            $resp = array('info' => true, 'message' => $price['message']);
        else
            $resp = array('success' => true, 'message' => 'Trm cargado correctamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
