<?php

use tezlikv3\dao\CompaniesLicenseDao;
use tezlikv3\dao\CompanyDao;
use tezlikv3\dao\GeneralCompanyLicenseDao;

$companyDao = new CompanyDao();
$companiesLicenseDao = new CompaniesLicenseDao();
$generalCompanyLicenseDao = new GeneralCompanyLicenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/company', function (Request $request, Response $response, $args) use ($companyDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $company = $companyDao->findDataCompanyByCompany($id_company);
    $response->getBody()->write(json_encode($company, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeDateContract/{op}', function (Request $request, Response $response, $args) use (
    $companiesLicenseDao,
    $generalCompanyLicenseDao
) {
    $company = [];
    if ($args['op'] == '1') {
        session_start();
        $company[0]['id_company'] = $_SESSION['id_company'];
        date_default_timezone_set('America/Bogota');
        $date = date('Y-m-d H:i:s');
    } else {
        $company = $companiesLicenseDao->findCompanyLicenseActive();
        $date = null;
    }

    $contract = null;

    for ($i = 0; $i < sizeof($company); $i++) {
        $contract = $generalCompanyLicenseDao->changeDateContract($company[$i]['id_company'], $date);

        if (isset($contract['info'])) break;
    }

    if ($contract == null) {
        if ($args['op'] == 1) {
            $resp = array('success' => true, 'message' => 'Información guardada correctamente');
            $_SESSION['date_contract'] = $date;
        } else
            $resp = array('success' => true, 'message' => 'Información enviada correctamente');
    } else if (isset($contract['info']))
        $resp = array('error' => true, 'message' => $contract['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la informacion. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/*$app->post('/addCompany', function (Request $request, Response $response, $args) use (
    $companyDao,
    $userDao,
    $licenseCompanyDao,
    $generateCodeDao,
    $makeEmailDao,
    $sendEmailDao
) {
    $data = $request->getParsedBody();

    if (
        empty($data['company']) && empty($data['state']) &&
        empty($data['city']) && empty($data['country']) && empty($data['address']) &&
        empty($data['telephone']) && empty($data['nit']) && empty($data['creador'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese los todos datos');
    else {
        $id_company = $companyDao->insertCompany($data);

        $newPass = $generateCodeDao->GenerateCode();

        // Se envia email con usuario(email) y contraseña
        $dataEmail = $makeEmailDao->SendEmailForgotPassword($dataUser['emailUser'], $newPass);

        $sendEmail = $sendEmailDao->sendEmail($dataEmail, $email, $name);

        if (!$sendEmail['info']) {
            $pass = password_hash($newPass, PASSWORD_DEFAULT);

            // Almacena el usuario
            $users = $userDao->saveUser($dataUser, $pass, $id_company);
        }
        $licenseCompany = $licenseCompanyDao->insertLicenseCompanyByCompany($data, $id_company);

        if ($licenseCompany == null)
            $resp = array('success' => true, 'message' => 'Compañia ingresada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Error al crear Empresa. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCompany', function (Request $request, Response $response, $args) use ($companyDao) {
    $dataCompany = $request->getParsedBody();

    if (
        empty($dataCompany['nameCommercial']) && empty($dataCompany['company']) && empty($dataCompany['state']) &&
        empty($dataCompany['city']) && empty($dataCompany['country']) && empty($dataCompany['address']) &&
        empty($dataCompany['telephone']) && empty($dataCompany['nit']) && empty($dataCompany['creador'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        $company = $companyDao->updateCompany($dataCompany);
        if ($company == null)
            $resp = array('success' => true, 'message' => 'Compañia actualizada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
}); 

$app->get('/deleteCompany/{id_company}', function (Request $request, Response $response, $args) use ($companyDao) {
    $company = $companyDao->deleteCompany($args['id_company']);

    if ($company == null)
        $resp = array('success' => true, 'message' => 'Compañia eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la compañia, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
}); */
