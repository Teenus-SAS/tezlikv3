<?php

use tezlikv3\dao\{
    LicenseCompanyDao,
    PriceEURDao,
    ProductsDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consultar Euro actual */

$app->get('/priceEUR/{coverage_eur}', function (Request $request, Response $response, $args) {

    $priceEURDao = new PriceEURDao();
    $productsDao = new ProductsDao();
    $licenceCompanyDao = new LicenseCompanyDao();

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


    // Modificar valor de cobertura y numero de desviacion en la tabla companies_licences
    $resolution = $priceEURDao->updateLastEuroCoverage($coverage_eur, $deviation, $id_company);

    $_SESSION['coverage_eur'] = $coverage_eur;

    if ($resolution == null)
        $resp = array('success' => true, 'coverage_eur' => $coverage_eur, 'deviation' => $deviation, 'exchangeCoverage' => 0);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
