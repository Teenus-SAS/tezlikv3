<?php

use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\MaterialsDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\Dao\TrmDao;
use tezlikv3\dao\WebTokenDao;

$trmDao = new TrmDao();
$pricesUSDDao = new PriceUSDDao();
$webTokenDao = new WebTokenDao();
$productsDao = new ProductsDao();
$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$licenceCompanyDao = new LicenseCompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consultar dolar actual */

$app->get('/currentDollar', function (Request $request, Response $response, $args) use (
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

    $price = $trmDao->getLastTrm();
    if ($price == 1) {
        $resp = ['error' => true, 'message' => 'Error al guardar la información. Intente mas tarde'];

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($price, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Calcular valor de cobertura simulacion */
$app->post('/simPriceUSD', function (Request $request, Response $response, $args) use (
    $pricesUSDDao,
    $webTokenDao,
    $productsDao,
    $trmDao
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

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataTrm = $request->getParsedBody();

    $deviation = $dataTrm['deviation'];
    $coverage_usd = $dataTrm['coverage_usd'];
    $op = $dataTrm['id'];

    if ($op == 'deviation') {
        // Calcular Promedio TRM
        $price = $pricesUSDDao->calcAverageTrm();

        // Obtener trm historico
        $historicalTrm = $trmDao->findAllHistoricalTrm();

        // Calcular desviacion estandar
        $standardDeviation = $pricesUSDDao->calcStandardDeviation($historicalTrm);

        // Calcular valor de cobertura
        $coverage_usd = $pricesUSDDao->calcDollarCoverage($price['average_trm'], $standardDeviation, $deviation);
    }

    // Obtener trm actual
    $actualTrm = $trmDao->getLastTrm();

    if ($actualTrm == 1) {
        $resp = ['error' => true, 'message' => 'Error al guardar la información. Intente mas tarde'];

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    // Covertura Cambiaria
    $exchangeCoverage = $actualTrm[0]['valor'] - $coverage_usd;

    $resp = array('success' => true, 'coverage_usd' => $coverage_usd, 'exchangeCoverage' => $exchangeCoverage);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/priceUSD/{coverage_usd}', function (Request $request, Response $response, $args) use (
    $licenceCompanyDao,
    $webTokenDao,
    $pricesUSDDao,
    $productsDao,
    $generalMaterialsDao,
    $trmDao
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

    // session_start();
    $id_company = $_SESSION['id_company'];

    $company = $licenceCompanyDao->findLicenseCompany($id_company);

    $deviation = $company['deviation'];
    $coverage_usd = $args['coverage_usd'];

    // Obtener productos
    $products = $productsDao->findAllProductsByCompany($id_company);

    for ($i = 0; $i < sizeof($products); $i++) {
        // Calcular precio USD y modificar
        $resolution = $pricesUSDDao->calcPriceUSDandModify($products[$i], $coverage_usd);

        if (isset($resolution['info'])) break;
    }

    // Obtener trm actual
    $actualTrm = $trmDao->getLastTrm();

    if ($actualTrm == 1) {
        $resp = ['error' => true, 'message' => 'Error al guardar la información. Intente mas tarde'];

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    // Covertura Cambiaria
    $exchangeCoverage = $actualTrm[0]['valor'] - $coverage_usd;

    // Modificar valor de cobertura y numero de desviacion en la tabla companies_licences
    $resolution = $pricesUSDDao->updateLastDollarCoverage($coverage_usd, $deviation, $id_company);

    $_SESSION['coverage_usd'] = $coverage_usd;

    // Materiales
    $materials = $generalMaterialsDao->findAllMaterialsUSDByCompany($id_company);

    foreach ($materials as $arr) {
        $formatCoverage = sprintf('$%s', number_format($coverage_usd, 2, ',', '.'));

        $trm = $actualTrm[0]['valor'];
        $formatTrm = sprintf('$%s', number_format($trm, 2, ',', '.'));

        $cost_cop = $arr['cost'];
        $cost_usd = floatval($cost_cop) / floatval($coverage_usd);
        $formatCost = sprintf('$%s', number_format($cost_usd, 2, ',', '.'));

        $data = [];
        $data['date'] = date('Y-m-d');
        $data['observation'] = "Precio en Dolares: $formatCost. Valor del Dolar en la que se encuentra ahora: $formatCoverage. TRM Actual: $formatTrm";
        $data['idMaterial'] = $arr['id_material'];
        $data['cost_usd'] = $cost_usd;

        $resolution = $generalMaterialsDao->saveBillMaterial($data);
        if ($resolution != null) break;
        $resolution = $generalMaterialsDao->saveCostUSDMaterial($data);
        if ($resolution != null) break;

        if ($_SESSION['export_import'] == '1') {
            $data['costImport'] = $arr['cost_import'];
            $data['costExport'] = $arr['cost_export'];
            $resolution = $generalMaterialsDao->saveAllCostsUSDMaterial($data);
            if ($resolution != null) break;

            $data['costImport'] = floatval($data['costImport']) * floatval($coverage_usd);
            $data['costExport'] = floatval($data['costExport']) * floatval($coverage_usd);
            $data['costTotal'] = $arr['cost'] + $data['costImport'] + $data['costExport'];

            $resolution = $generalMaterialsDao->saveCostsMaterial($data);
            if ($resolution != null) break;
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'coverage_usd' => $coverage_usd, 'deviation' => $deviation, 'exchangeCoverage' => $exchangeCoverage);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
