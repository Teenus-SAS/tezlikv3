<?php

use tezlikv3\dao\ContactsDao;
use tezlikv3\dao\WebTokenDao;

$contactsDao = new ContactsDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/contacts', function (Request $request, Response $response, $args) use (
    $contactsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $contacts = $contactsDao->findAllContacts($id_company);

    $response->getBody()->write(json_encode($contacts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/contactsByCompany/{id_company}', function (Request $request, Response $response, $args) use (
    $contactsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $contacts = $contactsDao->findAllContactsByCompany($args['id_company']);

    $response->getBody()->write(json_encode($contacts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addContact', function (Request $request, Response $response, $args) use (
    $contactsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $dataContact = $request->getParsedBody();

    if (
        empty($dataContact['firstname']) || empty($dataContact['lastname']) || empty($dataContact['phone']) ||
        empty($dataContact['email']) || empty($dataContact['position']) || empty($dataContact['idCompany'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $contacts = $contactsDao->insertContacts($dataContact);

        if ($contacts == null)
            $resp = array('success' => true, 'message' => 'Contacto ingresado correctamente');
        else if (isset($contacts['info']))
            $resp = array('info' => true, 'message' => $contacts['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateContact', function (Request $request, Response $response, $args) use (
    $contactsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $dataContact = $request->getParsedBody();

    if (
        empty($dataContact['idContact']) || empty($dataContact['firstname']) || empty($dataContact['lastname']) || empty($dataContact['phone']) ||
        empty($dataContact['email']) || empty($dataContact['position']) || empty($dataContact['idCompany'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $contacts = $contactsDao->updateContact($dataContact);

        if ($contacts == null)
            $resp = array('success' => true, 'message' => 'Contacto modificado correctamente');
        else if (isset($contacts['info']))
            $resp = array('info' => true, 'message' => $contacts['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteContact/{id_contact}', function (Request $request, Response $response, $args) use (
    $contactsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $contacts = $contactsDao->deleteContact($args['id_contact']);

    if ($contacts == null)
        $resp = array('success' => true, 'message' => 'Contacto eliminado correctamente');
    else if (isset($contacts['info']))
        $resp = array('info' => true, 'message' => $contacts['message']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar la información');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
