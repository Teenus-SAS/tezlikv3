<?php

use tezlikv3\dao\PucsDao;

$pucsDao = new PucsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/puc', function (RouteCollectorProxy $group) use ($pucsDao) {

    //Obtener Cuentas generales
    $group->get('', function (Request $request, Response $response, $args) use ($pucsDao) {
        $resp = $pucsDao->findAllCounts();
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());

    //Agregar Cuenta
    $group->post('/add', function (Request $request, Response $response, $args) use ($pucsDao) {
        $dataPuc = $request->getParsedBody();
        $respPuc = $pucsDao->insertCountsPUC($dataPuc);

        if ($respPuc == 1) {
            $resp = array('error' => true, 'message' => 'La cuenta ya existe en la base de datos');
        } elseif ($respPuc == null) {
            $resp = array('success' => true, 'message' => 'Datos ingresados correctamente');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        }
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());

    //Actualizar Cuenta
    $group->post('/update', function (Request $request, Response $response, $args) use ($pucsDao) {
        $dataPuc = $request->getParsedBody();
        $respPuc = $pucsDao->updateCountsPUC($dataPuc);

        if ($respPuc == null) {
            $resp = array('success' => true, 'message' => 'Datos actualizados correctamente');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
        }

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());
})->add(new SessionMiddleware());
