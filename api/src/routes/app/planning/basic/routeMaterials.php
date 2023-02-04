<?php

use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\PlanMaterialsDao;

$materialsDao = new PlanMaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/planMaterials', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $generalMaterialsDao->findAllMaterialsByCompany($id_company);
    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Materias prima importada */
$app->post('/planMaterialsDataValidation', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao
) {
    $dataMaterial = $request->getParsedBody();

    if (isset($dataMaterial)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $materials = $dataMaterial['importMaterials'];

        for ($i = 0; $i < sizeof($materials); $i++) {

            if (
                empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) || empty($materials[$i]['unityRawMaterial']) ||
                empty($materials[$i]['quantity']) || empty($materials[$i]['category'])
            ) {
                $i = $i + 1;
                $dataImportMaterial = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            } else {
                $findMaterial = $generalMaterialsDao->findMaterial($materials[$i], $id_company);
                if (!$findMaterial) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportMaterial['insert'] = $insert;
                $dataImportMaterial['update'] = $update;
            }
        }
    } else
        $dataImportMaterial = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportMaterial, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $generalMaterialsDao
) {
    session_start();
    $dataMaterial = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $dataMaterials = sizeof($dataMaterial);

    if ($dataMaterials > 1) {
        $materials = $materialsDao->insertMaterialsByCompany($dataMaterial, $id_company);

        if ($materials == null)
            $resp = array('success' => true, 'message' => 'Materia Prima creada correctamente');
        else if (isset($materials['info']))
            $resp = array('info' => true, 'message' => $materials['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $materials = $dataMaterial['importMaterials'];

        for ($i = 0; $i < sizeof($materials); $i++) {
            $material = $generalMaterialsDao->findMaterial($materials[$i], $id_company);

            if (!$material)
                $resolution = $materialsDao->insertMaterialsByCompany($materials[$i], $id_company);
            else {
                $materials[$i]['idMaterial'] = $material['id_material'];
                $resolution = $materialsDao->updateMaterialsByCompany($materials[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia Prima Importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao
) {
    $dataMaterial = $request->getParsedBody();

    $materials = $materialsDao->updateMaterialsByCompany($dataMaterial);

    if ($materials == null)
        $resp = array('success' => true, 'message' => 'Materia Prima actualizada correctamente');
    else if (isset($materials['info']))
        $resp = array('info' => true, 'message' => $materials['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanMaterial/{id_material}', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao
) {
    $materials = $generalMaterialsDao->deleteMaterial($args['id_material']);
    if ($materials == null)
        $resp = array('success' => true, 'message' => 'Material eliminado correctamente');

    if ($materials != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar el material, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
