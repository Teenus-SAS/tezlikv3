<?php

use tezlikv3\dao\ContactsDao;

$contactsDao = new ContactsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/contacts', function (Request $request, Response $response, $args) use ($contactsDao) {
    $contacts = $contactsDao->findAllContacts();

    $response->getBody()->write(json_encode($contacts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addContact', function (Request $request, Response $response, $args) use ($contactsDao) {
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

$app->post('/updateContact', function (Request $request, Response $response, $args) use ($contactsDao) {
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

$app->get('/deleteContact/{id_contact}', function (Request $request, Response $response, $args) use ($contactsDao) {
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