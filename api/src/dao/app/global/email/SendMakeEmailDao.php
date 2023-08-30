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

    public function SendEmailForgotPassword($email, $password)
    {
        // the message
        $msg  =
            "<html>
                <body>
                    <p>
                        Hola,<br>
                        Recientemente solicitó recordar su contraseña por lo que para mayor seguridad creamos una nueva. Para ingresar a Tezlik puede hacerlo con:<br>
                        <ul>
                            <li>Nombre de usuario:  $email</li>
                            <li>Contraseña: $password</li>
                        </ul><br>
                        Las contraseñas generadas a través de nuestra plataforma son muy seguras solo se envían al correo electrónico del contacto de la cuenta.
                        Si le preocupa la seguridad de la cuenta o sospecha que alguien está intentando obtener acceso no autorizado, puede estar 
                        seguro que las contraseñas son generadas aleatoriamente, sin embargo, le recomendamos ingresar a la plataforma con la nueva clave y cambiarla por una nueva.
                        Saludos,<br><br>
                        Equipo de Soporte Tezlik
                    </p>
                </body>
            </html>";

        $resp = array('to' => array($email), 'pdf' => null, 'subject' => 'Nuevo Password', 'body' => $msg, 'ccHeader' => null);
        return $resp;
    }

    public function SendEmailPassword($email, $password)
    {
        $msg  =
            "<html>
                <body>
                    <p>
                        Hola,<br>
                        Recientemente se creo una nueva cuenta. Para ingresar a Tezlik puede hacerlo con:<br>
                        <ul>
                            <li>Nombre de usuario:  $email</li>
                            <li>Contraseña: $password</li>
                        </ul><br>
                        Las contraseñas generadas a través de nuestra plataforma son muy seguras solo se envían al correo electrónico del contacto de la cuenta.
                        Si le preocupa la seguridad de la cuenta o sospecha que alguien está intentando obtener acceso no autorizado, puede estar 
                        seguro que las contraseñas son generadas aleatoriamente, sin embargo, le recomendamos ingresar a la plataforma con la nueva clave y cambiarla por una nueva.
                        Saludos,<br><br>
                        Equipo de Soporte Tezlik
                    </p>
                </body>
            </html>";
        $resp = array('to' => array($email), 'pdf' => null, 'subject' => 'Nuevo Password', 'body' => $msg, 'ccHeader' => null);
        return $resp;
    }

    public function SendEmailNotifications($name, $email, $observations)
    {
        $msg  =
            "<html>
                <body>
                    <p>
                        Estimado(a) $name<br><br>
                        Estamos muy emocionados de anunciar una nueva actualización para TezlikSoftware. Esta actualización incluye una serie de mejoras y funciones nuevas que estamos seguros de que te encantarán.<br><br>
                        Aquí hay un resumen de los cambios más importantes:
                        <ul>
                            <li>$observations</li>
                        </ul><br><br>
                        Estamos seguros que estas mejoras harán que TezlikSoftware sea más útil y agradable para usted. Si tiene alguna pregunta o inconveniente, no dude en contactarnos.<br><br>
                        Le enviamos un cordial saludo.<br><br>
                        Equipo de Soporte<br>
                        Teenus SAS
                    </p>
                </body>
            </html>";
        $resp = array('to' => array($email), 'pdf' => null, 'subject' => 'Actualización', 'body' => $msg, 'ccHeader' => null);
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
