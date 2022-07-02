<?php

use tezlikv3\dao\InvMoldsDao;

$invMoldsDao = new InvMoldsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/invMolds', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $invMolds = $invMoldsDao->findAllInvMold($id_company);
    $response->getBody()->write(json_encode($invMolds, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/invMoldDataValidation', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    $dataMold = $request->getParsedBody();

    if (isset($dataMold)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $molds = $dataMold['importInvMold'];

        for ($i = 0; $i < sizeof($molds); $i++) {
            if (empty($molds[$i]['referenceMold']) || empty($molds[$i]['mold']) || empty($molds[$i]['assemblyTime'])) {
                $i = $i + 1;
                $dataImportInvMold = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            } else {
                $findMold = $invMoldsDao->findInvMold($molds[$i], $id_company);
                !$findMold ? $insert = $insert + 1 : $update = $update + 1;
                $dataImportInvMold['insert'] = $insert;
                $dataImportInvMold['update'] = $update;
            }
        }
    } else
        $dataImportInvMold = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportInvMold, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addMold', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMold = $request->getParsedBody();

    $dataMolds = sizeof($dataMold);

    if ($dataMolds > 1) {
        $invMolds = $invMoldsDao->insertInvMoldByCompany($dataMold, $id_company);

        if ($invMolds == null) $resp = array('success' => true, 'message' => 'Molde creado correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $molds = $dataMold['importInvMold'];

        for ($i = 0; $i < sizeof($molds); $i++) {
            $findMold = $invMoldsDao->findInvMold($molds[$i], $id_company);

            if (!$findMold) $resolution = $invMoldsDao->insertInvMoldByCompany($molds[$i], $id_company);
            else {
                $molds[$i]['idMold'] = $findMold['id_mold'];
                $resolution = $invMoldsDao->updateInvMold($molds[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Moldes importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateMold', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    $dataMold = $request->getParsedBody();

    if (empty($dataMold['referenceMold']) || empty($dataMold['mold']) || empty($dataMold['assemblyTime']) || empty($dataMold['idMold'])) {
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    } else {
        $invMolds = $invMoldsDao->updateInvMold($dataMold);

        if ($invMolds == null)
            $resp = array('success' => true, 'message' => 'Molde modificado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteMold/{id_mold}', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    $invMolds = $invMoldsDao->deleteInvMold($args['id_mold']);
    if ($invMolds == null)
        $resp = array('success' => true, 'message' => 'Molde eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el molde, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
