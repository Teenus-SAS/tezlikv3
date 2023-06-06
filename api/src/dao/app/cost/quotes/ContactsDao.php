<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ContactsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllContacts()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT c.id_contact, c.firstname, c.lastname, c.phone, c.phone1, c.email, c.position, qc.id_quote_company, qc.company_name 
                                      FROM quote_customers c
                                      INNER JOIN quote_companies qc ON qc.id_quote_company = c.id_quote_company");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $contacts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $contacts;
    }

    public function findAllContactsByCompany($id_quote_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT c.id_contact, c.firstname, c.lastname, c.phone, c.phone1, c.email, c.position, qc.id_quote_company, qc.company_name 
                                      FROM quote_customers c
                                      INNER JOIN quote_companies qc ON qc.id_quote_company = c.id_quote_company
                                      WHERE c.id_quote_company = :id_quote_company");
        $stmt->execute(['id_quote_company' => $id_quote_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $contacts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $contacts;
    }

    public function insertContacts($dataContact)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO quote_customers (firstname, lastname, phone, phone1, email, position, id_quote_company) 
                                          VALUES (:firstname, :lastname, :phone, :phone1, :email, :position, :id_quote_company)");
            $stmt->execute([
                'firstname' => $dataContact['firstname'],
                'lastname' => $dataContact['lastname'],
                'phone' => $dataContact['phone'],
                'phone1' => $dataContact['phone1'],
                'email' => $dataContact['email'],
                'position' => $dataContact['position'],
                'id_quote_company' => $dataContact['idCompany']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateContact($dataContact)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE quote_customers SET firstname = :firstname, lastname = :lastname, phone = :phone, phone1 = :phone1, 
                                                                email = :email, position = :position, id_quote_company = :id_quote_company
                                          WHERE id_contact = :id_contact");
            $stmt->execute([
                'id_contact' => $dataContact['idContact'],
                'firstname' => $dataContact['firstname'],
                'lastname' => $dataContact['lastname'],
                'phone' => $dataContact['phone'],
                'phone1' => $dataContact['phone1'],
                'email' => $dataContact['email'],
                'position' => $dataContact['position'],
                'id_quote_company' => $dataContact['idCompany']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteContact($id_contact)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM quote_customers WHERE id_contact = :id_contact");
            $stmt->execute(['id_contact' => $id_contact]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM quote_customers WHERE id_contact = :id_contact");
                $stmt->execute(['id_contact' => $id_contact]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
