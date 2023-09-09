<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\CompaniesDao;
use tezlikv3\dao\FilesDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\ProfileDao;

$profileDao = new ProfileDao();
$FilesDao = new FilesDao();
$usersDao = new AutenticationUserDao();
$companyDao = new CompaniesDao();
$licenseDao = new LicenseCompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/updateProfile', function (Request $request, Response $response, $args) use ($profileDao, $FilesDao, $usersDao, $licenseDao, $companyDao) {
    session_start();
    $dataUser = $request->getParsedBody();

    if ($dataUser['admin'] == 1) {
        $profile = $profileDao->updateProfileAdmin($dataUser);
        if (sizeof($_FILES) > 0) $FilesDao->avatarUserAdmin($dataUser['idUser']);
    } else {
        $id_company = $_SESSION['id_company'];
        $profile = $profileDao->updateProfile($dataUser);

        if ($profile == null) {
            $profile = $companyDao->updateCompany($dataUser);

            if (sizeof($_FILES) > 0) {
                if (isset($_FILES['avatar']))
                    $FilesDao->avatarUser($dataUser['idUser'], $id_company);
                if (isset($_FILES['logo']))
                    $FilesDao->logoCompany($id_company);
            }
        }
    }

    if ($profile == null) {
        $user = $usersDao->findByEmail($dataUser['emailUser']);
        $dataCompany = $licenseDao->findLicenseCompany($user['id_company']);

        $_SESSION['name'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['logoCompany'] = $dataCompany['logo'];

        $resp = array('success' => true, 'message' => 'Perfil actualizado correctamente');
    } else if (isset($profile['info']))
        $resp = array('info' => true, 'message' => $profile['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
