<?php

use tezlikv3\Dao\TrmDao;
use tezlikv3\dao\WebTokenDao;

$trmDao = new TrmDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/historicalTrm', function (Request $request, Response $response, $args) use (
    $trmDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $trm = $trmDao->findAllHistoricalTrm();
    $response->getBody()->write(json_encode($trm));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/loadLastsTrm', function (Request $request, Response $response, $args) use (
    $trmDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $resp = $trmDao->deleteAllHistoricalTrm();
    $today = date('Y-m-d');

    if ($resp == null) {
        $historicalTrm = $trmDao->getAllHistoricalTrm();

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
