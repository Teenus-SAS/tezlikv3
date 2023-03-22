<?php

use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\ProductsCostDao;
use tezlikv3\Dao\TrmDao;

$trmDao = new TrmDao();
$pricesUSDDao = new PriceUSDDao();
$productsCostDao = new ProductsCostDao();
$licenceCompanyDao = new LicenseCompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consultar dolar actual */

$app->get('/currentDollar', function (Request $request, Response $response, $args) use ($trmDao) {
    $date = date('Y-m-d');

    $price = $trmDao->getActualTrm($date);
    $response->getBody()->write(json_encode($price, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Calcular valor de cobertura */
$app->get('/priceUSD/{deviation}', function (Request $request, Response $response, $args) use (
    $licenceCompanyDao,
    $pricesUSDDao,
    $productsCostDao,
    $trmDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    if ($_SESSION['price_usd'] == 1 && $_SESSION['plan_cost_price_usd'] == 1) {

        $company = $licenceCompanyDao->findLicenseCompany($id_company);

        if ($company['deviation'] == 0)  $deviation = 3;
        else if ($args['deviation'] == 0) $deviation = $company['deviation'];
        else $deviation = $args['deviation'];

        // Calcular Promedio TRM
        $price = $pricesUSDDao->calcAverageTrm();

        // Obtener trm historico
        $historicalTrm = $trmDao->findAllHistoricalTrm();

        // Calcular desviacion estandar
        $standardDeviation = $pricesUSDDao->calcStandardDeviation($historicalTrm);

        // Calcular valor de cobertura
        $coverage = $pricesUSDDao->calcDollarCoverage($price['average_trm'], $standardDeviation, $deviation);

        // Obtener productos
        $products = $productsCostDao->findAllProductsCost($id_company);

        for ($i = 0; $i < sizeof($products); $i++) {
            // Calcular precio USD y modificar
            $resolution = $pricesUSDDao->calcPriceUSDandModify($products[$i], $coverage, $id_company);

            if (isset($resolution['info'])) break;
        }

        // Obtener trm actual
        $date = date('Y-m-d');
        $price = $trmDao->getActualTrm($date);

        // Covertura Cambiaria
        $exchangeCoverage = $price - $coverage;

        // Modificar valor de cobertura y numero de desviacion en la tabla companies_licences
        $resolution = $pricesUSDDao->updateLastDollarCoverage($coverage, $deviation, $id_company);
    }

    if (isset($resolution) == null)
        $resp = array('success' => true, 'coverage' => $coverage, 'deviation' => $deviation, 'exchangeCoverage' => $exchangeCoverage);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
