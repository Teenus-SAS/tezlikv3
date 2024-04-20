<?php

use tezlikv3\dao\GeneralPCenterDao;
use tezlikv3\dao\ProductionCenterDao;

$productionCenterDao = new ProductionCenterDao();
$generalPCenterDao = new GeneralPCenterDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/productionCenter', function (Request $request, Response $response, $args) use ($productionCenterDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $productions = $productionCenterDao->findAllPCenterByCompany($id_company);
    $response->getBody()->write(json_encode($productions, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->post('/processDataValidation', function (Request $request, Response $response, $args) use ($generalProcessDao) {
//     $dataPCenter = $request->getParsedBody();

//     if (isset($dataPCenter)) {
//         session_start();
//         $id_company = $_SESSION['id_company'];

//         $insert = 0;
//         $update = 0;

//         $process = $dataPCenter['importProcess'];

//         for ($i = 0; $i < sizeof($process); $i++) {
//             if (empty($process[$i]['process'])) {
//                 $i = $i + 2;
//                 $dataImportProcess = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
//                 break;
//             }
//             if (empty(trim($process[$i]['process']))) {
//                 $i = $i + 2;
//                 $dataImportProcess = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
//                 break;
//             } else {
//                 $findProcess = $generalProcessDao->findProcess($process[$i], $id_company);
//                 if (!$findProcess) $insert = $insert + 1;
//                 else $update = $update + 1;
//                 $dataImportProcess['insert'] = $insert;
//                 $dataImportProcess['update'] = $update;
//             }
//         }
//     } else
//         $dataImportProcess = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

//     $response->getBody()->write(json_encode($dataImportProcess, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/addPCenter', function (Request $request, Response $response, $args) use (
//     $processDao,
//     $generalProcessDao
// ) {
//     session_start();
//     $dataPCenter = $request->getParsedBody();
//     $id_company = $_SESSION['id_company'];

//     if (empty($dataPCenter['importProcess'])) {

//         $process = $generalProcessDao->findProcess($dataPCenter, $id_company);

//         if (!$process) {
//             $process = $processDao->insertProcessByCompany($dataPCenter, $id_company);

//             if ($process == null)
//                 $resp = array('success' => true, 'message' => 'Proceso creado correctamente');
//             else if (isset($process['info']))
//                 $resp = array('info' => true, 'message' => $process['message']);
//             else
//                 $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
//         } else
//             $resp = array('info' => true, 'message' => 'Proceso duplicado. Ingrese una nuevo proceso');
//     } else {
//         // $process = $dataPCenter['importProcess'];

//         // for ($i = 0; $i < sizeof($process); $i++) {
//         //     if (isset($resolution['info'])) break;

//         //     $findProcess = $generalProcessDao->findProcess($process[$i], $id_company);
//         //     if (!$findProcess) {
//         //         $resolution = $processDao->insertProcessByCompany($process[$i], $id_company);

//         //         if (isset($resolution['info'])) break;

//         //         $lastInserted = $lastDataDao->lastInsertedProcessId($id_company);

//         //         $lastRoute = $generalProcessDao->findNextRoute($id_company);

//         //         $resolution = $generalProcessDao->changeRouteById($lastInserted['id_production_center'], $lastRoute['route']);
//         //     } else {
//         //         $process[$i]['idProcess'] = $findProcess['id_production_center'];
//         //         $resolution = $processDao->updateProcess($process[$i]);
//         //     }
//         // }
//         // if ($resolution == null)
//         //     $resp = array('success' => true, 'message' => 'Proceso importado correctamente');
//         // else if (isset($resolution['info']))
//         //     $resp = array('info' => true, 'message' => $resolution['message']);
//         // else
//         //     $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
//     }

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

// $app->post('/updatePCenter', function (Request $request, Response $response, $args) use (
//     $processDao,
//     $generalProcessDao
// ) {
//     session_start();
//     $dataPCenter = $request->getParsedBody();
//     $id_company = $_SESSION['id_company'];

//     $data = [];

//     $process = $generalProcessDao->findProcess($dataPCenter, $id_company);

//     !is_array($process) ? $data['id_production_center'] = 0 : $data = $process;

//     if ($data['id_production_center'] == $dataPCenter['idProcess'] || $data['id_production_center'] == 0) {
//         $process = $processDao->updateProcess($dataPCenter);

//         if ($process == null)
//             $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
//         else if (isset($process['info']))
//             $resp = array('info' => true, 'message' => $process['message']);
//         else
//             $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
//     } else
//         $resp = array('info' => true, 'message' => 'Proceso duplicado. Ingrese una nuevo proceso');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

// $app->get('/deletePCenter/{id_production_center}', function (Request $request, Response $response, $args) use ($processDao) {
//     $process = $processDao->deleteProcess($args['id_production_center']);

//     if ($process == null)
//         $resp = array('success' => true, 'message' => 'Proceso eliminado correctamente');

//     if ($process != null)
//         $resp = array('error' => true, 'message' => 'No es posible eliminar el proceso, existe información asociada a él');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });
