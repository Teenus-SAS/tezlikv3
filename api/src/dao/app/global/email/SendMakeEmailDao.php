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

        $msg = "Hola $name<br><br>
                Si estas tratando de iniciar sesion en Tezlik. <br>
                Ingresa el siguiente código para completar el inicio de sesión:<br><br>
                <h4>$code</h4>";
        // Headers
        /* $headers = "Tu código de verificación de inicio de sesión";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SoporteTeenus <soporte@teenus.com.co>" . "\r\n"; */

        $resp = array('to' => array($user['email']), 'img' => null, 'subject' => 'Código De Verificación', 'body' => $msg, 'ccHeader' => null);

        return $resp;
    }

    public function SendEmailPassword($email, $password)
    {
        // the message
        $msg = "Hola,<br><br>
            Recientemente solicitó recordar su contraseña por lo que para mayor seguridad creamos una nueva. Para ingresar a Tezlik puede hacerlo con:
            <ul>
            <li>Nombre de usuario: $email</li>
            <li>Contraseña: $password</li>
            </ul>
             
            Las contraseñas generadas a través de nuestra plataforma son muy seguras solo se envían al correo electrónico del contacto de la cuenta.<br><br> 
            Si le preocupa la seguridad de la cuenta o sospecha que alguien está intentando obtener acceso no autorizado, puede estar 
            seguro que las contraseñas son generadas aleatoriamente, sin embargo, le recomendamos ingresar a la plataforma con la nueva clave y cambiarla por una nueva.<br><br>
        
            Saludos,<br><br>
        
            Equipo de Soporte Tezlik";

        //headers
        //$headers = "MIME-Version: 1.0" . "\r\n";
        //$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        //$headers .= 'From: SoporteTezlik <soporteTezlik@tezliksoftware.com.co>' . "\r\n";
        // send email
        // mail($to, "Nuevo password", $msg, $headers);
        //$resp = $this->sendEmail($to, 'Nuevo Password', $headers, null, $msg, null);

        $resp = array('to' => array($email), 'img' => null, 'subject' => 'Nuevo Password', 'body' => $msg, 'ccHeader' => null);
        return $resp;
    }

    public function SendEmailSupport($dataSupport, $email)
    {
        if (isset($dataSupport['ccHeader']))
            $ccHeader = $dataSupport['ccHeader'];
        else $ccHeader = '';
        // the message
        $msg = $dataSupport['message'];

        // use wordwrap() if lines are longer than 70 characters
        // $msg = wordwrap($msg, 70);
        //subject
        $subject = 'Soporte' . "\r\n";
        $subject .= $dataSupport['subject'];
        //headers
        // $headers = "MIME-Version: 1.0" . "\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // $headers .= "From: SoporteTeenus <$email>" . "\r\n";

        $resp = array('to' => array($email), 'img' => null, 'subject' => $subject, 'body' => $msg, 'ccHeader' => $ccHeader);

        return $resp;
    }

    public function SendEmailQuote($dataQuote, $email)
    {
        $to = array($dataQuote['header'], $email);;
        if (isset($dataQuote['ccHeader']))
            $ccHeader = $dataQuote['ccHeader'];
        else $ccHeader = '';

        // the message
        $msg = $dataQuote['message'];

        //subject
        $subject = $dataQuote['subject'];

        //headers
        // $headers = "MIME-Version: 1.0" . "\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // $headers .= "From: <$email>" . "\r\n";

        $resp = array('to' => $to, 'img' => $dataQuote['img'], 'subject' => $subject, 'body' => $msg, 'ccHeader' => $ccHeader);

        return $resp;
    }
}
