<?php

use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();
$pricesUSDDao = new PriceUSDDao();
$productsDao = new ProductsDao();
$licenceCompanyDao = new LicenseCompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consultar dolar actual */

$app->get('/currentDollar', function (Request $request, Response $response, $args) use ($trmDao) {
    $price = $trmDao->getLastTrm();
    $response->getBody()->write(json_encode($price, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Calcular valor de cobertura simulacion */
$app->post('/simPriceUSD', function (Request $request, Response $response, $args) use (
    $pricesUSDDao,
    $productsDao,
    $trmDao
) {
    session_start();
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
    $pricesUSDDao,
    $productsDao,
    $trmDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $company = $licenceCompanyDao->findLicenseCompany($id_company);

    $deviation = $company['deviation'];
    $coverage = $args['coverage'];

    // Calcular Promedio TRM
    // $price = $pricesUSDDao->calcAverageTrm();

    // Obtener trm historico
    // $historicalTrm = $trmDao->findAllHistoricalTrm();

    // Calcular desviacion estandar
    // $standardDeviation = $pricesUSDDao->calcStandardDeviation($historicalTrm);

    // Calcular valor de cobertura
    // $coverage = $pricesUSDDao->calcDollarCoverage($price['average_trm'], $standardDeviation, $deviation);

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

    if ($resolution == null)
        $resp = array('success' => true, 'coverage' => $coverage, 'deviation' => $deviation, 'exchangeCoverage' => $exchangeCoverage);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
