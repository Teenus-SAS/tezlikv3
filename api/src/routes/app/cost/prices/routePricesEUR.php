<?php

use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\MaterialsDao;
use tezlikv3\Dao\PriceEURDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();
$priceEURDao = new PriceEURDao();
$productsDao = new ProductsDao();
$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$licenceCompanyDao = new LicenseCompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consultar Euro actual 

$app->get('/currentDollar', function (Request $request, Response $response, $args) use (
    $trmDao,
    $webTokenDao
) {
    

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $price = $trmDao->getLastTrm();
    $response->getBody()->write(json_encode($price, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
}); */

/* Calcular valor de cobertura simulacion 

$app->post('/simPriceEUR', function (Request $request, Response $response, $args) use (
    $pricesUSDDao,
    
    $productsDao,
    $trmDao
) {
    

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataTrm = $request->getParsedBody();

    $deviation = $dataTrm['deviation'];
    $coverage_eur = $dataTrm['coverage_eur'];
    $op = $dataTrm['id'];

    if ($op == 'deviation') {
        // Calcular Promedio TRM
        $price = $pricesUSDDao->calcAverageTrm();

        // Obtener trm historico
        $historicalTrm = $trmDao->findAllHistoricalTrm();

        // Calcular desviacion estandar
        $standardDeviation = $pricesUSDDao->calcStandardDeviation($historicalTrm);

        // Calcular valor de cobertura
        $coverage_eur = $pricesUSDDao->calcDollarCoverage($price['average_trm'], $standardDeviation, $deviation);
    }

    // Obtener trm actual
    $actualTrm = $trmDao->getLastTrm();

    // Covertura Cambiaria
    $exchangeCoverage = $actualTrm[0]['valor'] - $coverage_eur;

    $resp = array('success' => true, 'coverage_eur' => $coverage_eur, 'exchangeCoverage' => $exchangeCoverage);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
}); */

$app->get('/priceEUR/{coverage_eur}', function (Request $request, Response $response, $args) use (
    $licenceCompanyDao,
    $priceEURDao,
    $productsDao,
    $generalMaterialsDao,
    $trmDao
) {
    $id_company = $_SESSION['id_company'];

    $company = $licenceCompanyDao->findLicenseCompany($id_company);

    $deviation = $company['deviation'];
    $coverage_eur = $args['coverage_eur'];

    // Obtener productos
    $products = $productsDao->findAllProductsByCompany($id_company);

    for ($i = 0; $i < sizeof($products); $i++) {
        // Calcular precio USD y modificar
        $resolution = $priceEURDao->calcPriceUSDandModify($products[$i], $coverage_eur);

        if (isset($resolution['info'])) break;
    }

    // Obtener trm actual
    // $actualTrm = $trmDao->getLastTrm();

    // Covertura Cambiaria
    // $exchangeCoverage = $actualTrm[0]['valor'] - $coverage_eur;

    // Modificar valor de cobertura y numero de desviacion en la tabla companies_licences
    $resolution = $priceEURDao->updateLastEuroCoverage($coverage_eur, $deviation, $id_company);

    $_SESSION['coverage_eur'] = $coverage_eur;

    /* Materiales
        $materials = $generalMaterialsDao->findAllMaterialsUSDByCompany($id_company);

        foreach ($materials as $arr) {
            $formatCoverage = sprintf('$%s', number_format($coverage_eur, 2, ',', '.'));

            $trm = $actualTrm[0]['valor'];
            $formatTrm = sprintf('$%s', number_format($trm, 2, ',', '.'));

            $cost_cop = $arr['cost'];
            $cost_usd = floatval($cost_cop) / floatval($coverage_eur);
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
    */

    if ($resolution == null)
        $resp = array('success' => true, 'coverage_eur' => $coverage_eur, 'deviation' => $deviation, 'exchangeCoverage' => 0);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
