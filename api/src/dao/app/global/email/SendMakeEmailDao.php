<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;


class SendMakeEmailDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    public function SendEmailCode($code, $user)
    {
        $name = $user['firstname'];

        $msg = "Hola $name\r\n
                Si estas tratando de iniciar sesion en Tezlik. \r\n
                Ingresa el siguiente código para completar el inicio de sesión:\r\n
                <h4>$code</h4>";

        $resp = array('to' => array($user['email']), 'pdf' => null, 'subject' => 'Código De Verificación', 'body' => $msg, 'ccHeader' => null);

        return $resp;
    }

    public function SendEmailPassword($email, $password)
    {
        // the message
        $msg = nl2br("Hola, \n
                      Recientemente solicitó recordar su contraseña por lo que para mayor seguridad creamos una nueva. Para ingresar a Tezlik puede hacerlo con: \n
                        · Nombre de usuario: $email \n
                        · Contraseña: $password \n
                      Las contraseñas generadas a través de nuestra plataforma son muy seguras solo se envían al correo electrónico del contacto de la cuenta.\n
                      Si le preocupa la seguridad de la cuenta o sospecha que alguien está intentando obtener acceso no autorizado, puede estar 
                      seguro que las contraseñas son generadas aleatoriamente, sin embargo, le recomendamos ingresar a la plataforma con la nueva clave y cambiarla por una nueva.\n
                      Saludos,\n\n
                      Equipo de Soporte Tezlik");
        $resp = array('to' => array($email), 'pdf' => null, 'subject' => 'Nuevo Password', 'body' => $msg, 'ccHeader' => null);
        return $resp;
    }

    public function SendEmailSupport($dataSupport, $email)
    {
        if (isset($dataSupport['ccHeader']))
            $ccHeader = $dataSupport['ccHeader'];
        else $ccHeader = '';
        // the message
        $msg = $dataSupport['message'];

        $subject = 'Soporte' . "\r\n";
        $subject .= $dataSupport['subject'];

        $resp = array('to' => array($email), 'pdf' => null, 'subject' => $subject, 'body' => $msg, 'ccHeader' => $ccHeader);

        return $resp;
    }

    public function SendEmailQuote($dataQuote, $email, $file)
    {
        $to = array($dataQuote['header'], $email);;
        if (isset($dataQuote['ccHeader']))
            $ccHeader = $dataQuote['ccHeader'];
        else $ccHeader = '';

        // the message
        $msg = $dataQuote['message'];

        //subject
        $subject = $dataQuote['subject'];

        $resp = array('to' => $to, 'pdf' => $file, 'subject' => $subject, 'body' => $msg, 'ccHeader' => $ccHeader);

        return $resp;
    }
}
