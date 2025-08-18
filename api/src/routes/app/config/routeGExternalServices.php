<?php

use tezlikv3\dao\GeneralExternalServicesDao;

$externalServicesDao = new GeneralExternalServicesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/generalServices', function (RouteCollectorProxy $group) use ($externalServicesDao) {

    $group->get('/generalExternalservices', function (Request $request, Response $response, $args) use ($externalServicesDao) {

        $id_company = $_SESSION['id_company'];
        $externalServices = $externalServicesDao->findAllExternalServices($id_company);
        $response->getBody()->write(json_encode($externalServices));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/generalExternalServiceDataValidation', function (Request $request, Response $response, $args) use ($externalServicesDao) {

        $dataExternalService = $request->getParsedBody();

        if (isset($dataExternalService)) {

            $id_company = $_SESSION['id_company'];

            $insert = 0;
            $update = 0;

            $externalService = $dataExternalService['importExternalService'];

            for ($i = 0; $i < sizeof($externalService); $i++) {
                if (empty($externalService[$i]['service']) || empty($externalService[$i]['costService'])) {
                    $i = $i + 2;
                    $dataImportExternalService = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                    break;
                }
                if (empty(trim($externalService[$i]['service'])) || empty(trim($externalService[$i]['costService']))) {
                    $i = $i + 2;
                    $dataImportExternalService = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                    break;
                } else {
                    $findExternalService = $externalServicesDao->findExternalService($externalService[$i], $id_company);
                    if (!$findExternalService) $insert = $insert + 1;
                    else $update = $update + 1;
                    $dataImportExternalService['insert'] = $insert;
                    $dataImportExternalService['update'] = $update;
                }
            }
        } else
            $dataImportExternalService = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

        $response->getBody()->write(json_encode($dataImportExternalService, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addGExternalService', function (Request $request, Response $response, $args) use ($externalServicesDao) {


        $id_company = $_SESSION['id_company'];
        $dataExternalService = $request->getParsedBody();

        $dataExternalServices = sizeof($dataExternalService);

        if ($dataExternalServices > 1) {

            $externalService = $externalServicesDao->findExternalService($dataExternalService, $id_company);

            if (!$externalService) {
                $externalServices = $externalServicesDao->insertExternalServicesByCompany($dataExternalService, $id_company);

                if ($externalServices == null)
                    $resp = array('success' => true, 'message' => 'Servicio externo ingresado correctamente');
                else if (isset($externalServices['info']))
                    $resp = array('info' => true, 'message' => $externalServices['message']);
                else
                    $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
            } else
                $resp = array('info' => true, 'message' => 'Servicio duplicado. Ingrese una nuevo servicio');
        } else {
            $externalService = $dataExternalService['importExternalService'];

            for ($i = 0; $i < sizeof($externalService); $i++) {
                $findExternalService = $externalServicesDao->findExternalService($externalService[$i], $id_company);

                if (!$findExternalService)
                    $resolution = $externalServicesDao->insertExternalServicesByCompany($externalService[$i], $id_company);
                else {
                    $externalService[$i]['idService'] = $findExternalService['id_general_service'];
                    $resolution = $externalServicesDao->updateExternalServices($externalService[$i]);
                }
            }
            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Servicio externo importado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
        }

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/updateGExternalService', function (Request $request, Response $response, $args) use ($externalServicesDao) {

        $id_company = $_SESSION['id_company'];

        $dataExternalService = $request->getParsedBody();

        $data = [];

        $externalService = $externalServicesDao->findExternalService($dataExternalService, $id_company);

        !is_array($externalService) ? $data['id_general_service'] = 0 : $data = $externalService;

        if ($data['id_general_service'] == $dataExternalService['idService'] || $data['id_general_service'] == 0) {
            $externalServices = $externalServicesDao->updateExternalServices($dataExternalService);

            if ($externalServices == null)
                $resp = array('success' => true, 'message' => 'Servicio externo actualizado correctamente');
            else if (isset($externalServices['info']))
                $resp = array('info' => true, 'message' => $externalServices['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Servicio duplicado. Ingrese una nuevo servicio');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/deleteGExternalService', function (Request $request, Response $response, $args) use ($externalServicesDao) {

        $dataExternalService = $request->getParsedBody();

        $externalServices = $externalServicesDao->deleteExternalService($dataExternalService['idService']);

        if ($externalServices == null)
            $resp = array('success' => true, 'message' => 'Servicio externo eliminado correctamente');
        else
            $resp = array('error' => true, 'message' => 'No es posible eliminar el servicio externo, existe información asociada a él');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
