<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class SendEmailDao
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
        $to = $user['email'];

        $msg = "Hola $name<br><br>
                Si estas tratando de iniciar sesion en Tezlik. <br>
                Ingresa el siguiente código para completar el inicio de sesión:<br><br>
                <h4>$code</h4>";
        $msg = wordwrap($msg, 70);

        // Headers
        $headers = "Tu código de verificación de inicio de sesión";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SoporteTeenus <soporte@teenus.com.co>" . "\r\n";

        // send email
        mail($to, 'Codigo', $msg, $headers);
    }

    public function SendEmailPassword($email, $password)
    {
        // the message
        $msg = "Hola,<br><br>
            Recientemente solicitó recordar su contraseña por lo que para mayor seguridad creamos una nueva. Para ingresar al CRM puede hacerlo con:
            <ul>
            <li>Nombre de usuario: $email</li>
            <li>Contraseña: $password</li>
            </ul>
             
            Las contraseñas generadas a través de nuestra plataforma son muy seguras solo se envían al correo electrónico del contacto de la cuenta.<br><br> 
            Si le preocupa la seguridad de la cuenta o sospecha que alguien está intentando obtener acceso no autorizado, puede estar 
            seguro que las contraseñas son generadas aleatoriamente, sin embargo, le recomendamos ingresar a la plataforma con la nueva clave y cambiarla por una nueva.<br><br>
        
            Saludos,<br><br>
        
            El Equipo de Soporte CRM";

        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg, 70);

        //headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: SoporteCRM <soporteCRM@proyecformas.com>' . "\r\n";
        // send email
        mail($email, "Nuevo password", $msg, $headers);
    }

    public function SendEmailSupport($dataSupport, $email)
    {
        $to = 'soporte@teenus.com.co';
        $ccHeader = $dataSupport['ccHeader'];
        // the message
        $msg = $dataSupport['message'];

        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg, 70);

        //headers
        $headers = $dataSupport['subject'] . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SoporteTeenus <$email>" . "\r\n";
        // send email
        mail($to, "Soporte", $msg, $headers, $ccHeader);
    }
}
