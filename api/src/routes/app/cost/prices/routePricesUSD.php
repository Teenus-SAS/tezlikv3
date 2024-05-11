<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\MaterialsDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();
$pricesUSDDao = new PriceUSDDao();
$autenticationDao = new AutenticationUserDao();
$productsDao = new ProductsDao();
$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$licenceCompanyDao = new LicenseCompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consultar dolar actual */

$app->get('/currentDollar', function (Request $request, Response $response, $args) use (
    $trmDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $price = $trmDao->getLastTrm();
    $response->getBody()->write(json_encode($price, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Calcular valor de cobertura simulacion */
$app->post('/simPriceUSD', function (Request $request, Response $response, $args) use (
    $pricesUSDDao,
    $autenticationDao,
    $productsDao,
    $trmDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataTrm = $request->getParsedBody();

    $deviation = $dataTrm['deviation'];
    $coverage = $dataTrm['coverage'];
    $op = $dataTrm['id'];

    if ($op == 'deviation') {
        // Calcular Promedio TRM
        $price = $pricesUSDDao->calcAverageTrm();

        // Obtener trm historico
        $historicalTrm = $trmDao->findAllHistoricalTrm();

        // Calcular desviacion estandar
        $standardDeviation = $pricesUSDDao->calcStandardDeviation($historicalTrm);

        // Calcular valor de cobertura
        $coverage = $pricesUSDDao->calcDollarCoverage($price['average_trm'], $standardDeviation, $deviation);
    }

    // Obtener trm actual
    $actualTrm = $trmDao->getLastTrm();

    // Covertura Cambiaria
    $exchangeCoverage = $actualTrm[0]['valor'] - $coverage;

    $resp = array('success' => true, 'coverage' => $coverage, 'exchangeCoverage' => $exchangeCoverage);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/priceUSD/{coverage}', function (Request $request, Response $response, $args) use (
    $licenceCompanyDao,
    $autenticationDao,
    $pricesUSDDao,
    $productsDao,
    $generalMaterialsDao,
    $trmDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $company = $licenceCompanyDao->findLicenseCompany($id_company);

    $deviation = $company['deviation'];
    $coverage = $args['coverage'];

    // Obtener productos
    $products = $productsDao->findAllProductsByCompany($id_company);

    for ($i = 0; $i < sizeof($products); $i++) {
        // Calcular precio USD y modificar
        $resolution = $pricesUSDDao->calcPriceUSDandModify($products[$i], $coverage);

        if (isset($resolution['info'])) break;
    }

    // Obtener trm actual
    $actualTrm = $trmDao->getLastTrm();

    // Covertura Cambiaria
    $exchangeCoverage = $actualTrm[0]['valor'] - $coverage;

    // Modificar valor de cobertura y numero de desviacion en la tabla companies_licences
    $resolution = $pricesUSDDao->updateLastDollarCoverage($coverage, $deviation, $id_company);

    $_SESSION['coverage'] = $coverage;

    // Materiales
    $materials = $generalMaterialsDao->findAllMaterialsUSDByCompany($id_company);

    foreach ($materials as $arr) {
        $formatCoverage = sprintf('$%s', number_format($coverage, 2, ',', '.'));

        $trm = $actualTrm[0]['valor'];
        $formatTrm = sprintf('$%s', number_format($trm, 2, ',', '.'));

        $cost_cop = $arr['cost'];
        $cost_usd = floatval($cost_cop) / floatval($coverage);
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
    }

    if ($resolution == null)
        $resp = array('success' => true, 'coverage' => $coverage, 'deviation' => $deviation, 'exchangeCoverage' => $exchangeCoverage);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
