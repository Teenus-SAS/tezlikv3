<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use PHPMailer\PHPMailer\PHPMailer;

class SendEmailDao extends PHPMailer
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // public function sendEmail()
    // {
    //     try {
    //         // Contenido del correo
    //         $asunto    = clean($_POST["asunto"]);
    //         $contenido = clean($_POST["contenido"]);
    //         $para      = clean($_POST["destinatario"]);

    //         if (!filter_var($para, FILTER_VALIDATE_EMAIL)) {
    //             // throw new \Exception('Dirección de correo electrónico no válida.');
    //         }

    //         // Intancia de PHPMailer

    //         // Es necesario para poder usar un servidor SMTP como gmail
    //         $this->isSMTP();

    //         // Si estamos en desarrollo podemos utilizar esta propiedad para ver mensajes de error
    //         //SMTP::DEBUG_OFF    = off (for production use) 0
    //         //SMTP::DEBUG_CLIENT = client messages 1 
    //         //SMTP::DEBUG_SERVER = client and server messages 2
    //         $this->SMTPDebug     = SMTP::DEBUG_SERVER;

    //         //Set the hostname of the mail server
    //         $this->Host          = 'smtp.gmail.com';
    //         $this->Port          = 465; // o 587

    //         // Propiedad para establecer la seguridad de encripción de la comunicación
    //         $this->SMTPSecure    = PHPMailer::ENCRYPTION_SMTPS; // tls o ssl para gmail obligado

    //         // Para activar la autenticación smtp del servidor
    //         $this->SMTPAuth      = true;

    //         // Credenciales de la cuenta
    //         $email              = 'tucorreo@gmail.com';
    //         $this->Username     = $email;
    //         $this->Password     = 'tucontraseña';

    //         // Quien envía este mensaje
    //         $this->setFrom($email, 'Roberto Orozco');

    //         // Si queremos una dirección de respuesta
    //         $mail->addReplyTo('replyto@panchos.com', 'Pancho Doe');

    //         // Destinatario
    //         $mail->addAddress($para, 'John Doe');

    //         // Asunto del correo
    //         $mail->Subject = $asunto;

    //         // Contenido
    //         $mail->IsHTML(true);
    //         $mail->CharSet = 'UTF-8';
    //         $mail->Body    = sprintf('<h1>El mensaje es:</h1><br><p>%s</p>', $contenido);

    //         // Texto alternativo
    //         $mail->AltBody = 'No olvides suscribirte a nuestro canal.';

    //         // Agregar algún adjunto
    //         //$mail->addAttachment(IMAGES_PATH.'logo.png');

    //         // Enviar el correo
    //         if (!$mail->send()) {
    //             throw new \Exception($mail->ErrorInfo);
    //         }
    //     } catch (\Exception $e) {
    //         $message = $e->getMessage();

    //         $error = array('info' => true, 'message' => $message);
    //     }
    // }

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
        $to = 'soporte@tezliksoftware.com.co';
        $to .= $email;
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

        // use wordwrap() if lines are longer than 70 characters
        // $msg = wordwrap($msg, 70);

        //headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: SoporteTezlik <soporteTezlik@tezliksoftware.com.co>' . "\r\n";
        // send email
        mail($to, "Nuevo password", $msg, $headers);
    }

    public function SendEmailSupport($dataSupport, $email)
    {
        $to = 'soporte@teenus.com.co';
        $to .= $email;
        if (isset($dataSupport['ccHeader']))
            $ccHeader = $dataSupport['ccHeader'];
        else $ccHeader = '';
        // the message
        $msg = $dataSupport['message'];

        // use wordwrap() if lines are longer than 70 characters
        // $msg = wordwrap($msg, 70);

        //headers
        $headers = $dataSupport['subject'] . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SoporteTeenus <$email>" . "\r\n";
        // send email
        mail($to, "Soporte", $msg, $headers, $ccHeader);
    }

    public function SendEmailQuote($dataQuote, $email)
    {
        $to = $dataQuote['header'] . "\n";
        $to .= $email;
        if (isset($dataQuote['ccHeader']))
            $ccHeader = $dataQuote['ccHeader'];
        else $ccHeader = '';
        // the message
        $msg = $dataQuote['message'] . "\n";
        $msg .= $dataQuote['img'];

        //headers
        $headers = $dataQuote['subject'] . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: <$email>" . "\r\n";
        // send email
        mail($to, $msg, $headers, $ccHeader);
    }
}
