<?php

use tezlikv3\dao\UnitsDao;

$unitsDao = new UnitsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/units', function (RouteCollectorProxy $group) use ($unitsDao) {

    $group->get('/{id_magnitude}', function (Request $request, Response $response, $args) use ($unitsDao) {
        $units = $unitsDao->findUnitsByMagnitude($args['id_magnitude']);
        $response->getBody()->write(json_encode($units));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/add', function (Request $request, Response $response, $args) use ($unitsDao) {
        $dataUnit = $request->getParsedBody();

        $units = $unitsDao->insertUnit($dataUnit);

        if ($units == null)
            $resp = array('success' => true, 'message' => 'Unidad ingresada correctamente');
        else if (isset($units['info']))
            $resp = array('info' => true, 'message' => $units['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/update', function (Request $request, Response $response, $args) use ($unitsDao) {
        $dataUnit = $request->getParsedBody();

        $units = $unitsDao->updateUnit($dataUnit);

        if ($units == null)
            $resp = array('success' => true, 'message' => 'Unidad modificada correctamente');
        else if (isset($units['info']))
            $resp = array('info' => true, 'message' => $units['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/delete/{id_unit}', function (Request $request, Response $response, $args) use ($unitsDao) {
        $units = $unitsDao->deleteUnit($args['id_unit']);

        if ($units == null)
            $resp = array('success' => true, 'message' => 'Unidad eliminada correctamente');
        else if (isset($units['info']))
            $resp = array('info' => true, 'message' => $units['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al eliminar la unidad. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
