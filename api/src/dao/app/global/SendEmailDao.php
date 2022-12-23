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

    public function sendEmail($to, $subject, $header, $ccHeader, $message, $img)
    {
        require_once dirname(dirname(dirname(dirname(dirname(__DIR__))))) . "/env.php";

        try {
            /* if (!isset($_SESSION))
                session_start();
            $email = $_SESSION['email']; */


            // Intancia de PHPMailer

            // Es necesario para poder usar un servidor SMTP como gmail
            $this->isSMTP();

            // Si estamos en desarrollo podemos utilizar esta propiedad para ver mensajes de error
            //SMTP::DEBUG_OFF    = off (for production use) 0
            //SMTP::DEBUG_CLIENT = client messages 1 
            //SMTP::DEBUG_SERVER = client and server messages 2
            // $this->SMTPDebug     = 2;

            //Set the hostname of the mail server
            //$this->Host          = 'smtp.gmail.com';
            //$this->Port          = 465; // o 587

            $this->Host          = $_ENV["smtpHost"];
            $this->Port          = $_ENV["smtpPort"];

            // Propiedad para establecer la seguridad de encripción de la comunicación
            // $this->SMTPSecure    = PHPMailer::ENCRYPTION_SMTPS; // tls o ssl para gmail obligado
            //$this->SMTPSecure = "ssl";
            $this->SMTPSecure = "tls";

            // Para activar la autenticación smtp del servidor
            $this->SMTPAuth      = true;

            // Credenciales de la cuenta
            //$this->Username     = 'pruebaSoft145@gmail.com';
            //$this->Password     = 'glvgveacpopppjws';
            $this->Username     = $_ENV["smtpEmail"];;
            $this->Password     = $_ENV["smtpPass"];

            // Quien envía este mensaje
            //$this->setFrom($email);
            $this->setFrom($_ENV["smtpEmail"]);

            // Si queremos una dirección de respuesta
            // $this->addReplyTo('replyto@panchos.com', 'Pancho Doe');

            // Destinatario
            foreach ($to as $key => $value) {
                $this->addAddress($value);
            }

            // Asunto del correo
            if (isset($ccHeader) && !empty($ccHeader))
                $this->addCC($ccHeader);

            $this->Subject = $subject;

            // Imagen
            if ($img != null)
                $this->addStringAttachment(file_get_contents($img), 'Cotización.png');

            // Contenido
            $this->IsHTML(true);
            $this->CharSet = 'UTF-8';
            $this->Body    = sprintf($message);

            // Texto alternativo
            $this->mailHeader = $header;

            // Agregar algún adjunto
            //$mail->addAttachment(IMAGES_PATH.'logo.png');

            // Enviar el correo
            // if (!$this->send()) {
            //     throw new \Exception($this->ErrorInfo);
            // }
            $this->send();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function SendEmailCode($code, $user)
    {
        $name = $user['firstname'];
        $to = array($user['email']);

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
        // mail($to, 'Codigo', $msg, $headers);
        $resp = $this->sendEmail($to, 'Codigo', $headers, null, $msg, null);

        return $resp;
    }

    public function SendEmailPassword($email, $password)
    {
        $to = array('soporte@tezliksoftware.com.co', $email);
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
        // mail($to, "Nuevo password", $msg, $headers);
        $resp = $this->sendEmail($to, 'Nuevo Password', $headers, null, $msg, null);
        return $resp;
    }

    public function SendEmailSupport($dataSupport, $email)
    {
        $to = array('soporte@teenus.com.co', $email);
        if (isset($dataSupport['ccHeader']))
            $ccHeader = $dataSupport['ccHeader'];
        else $ccHeader = '';
        // the message
        $msg = $dataSupport['message'];

        // use wordwrap() if lines are longer than 70 characters
        // $msg = wordwrap($msg, 70);
        //subject
        $subject = 'Soporte';
        $subject .= $dataSupport['subject'];
        //headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SoporteTeenus <$email>" . "\r\n";
        // send email
        // mail($to, "Soporte", $msg, $headers, $ccHeader);
        $resp = $this->sendEmail($to, $subject, $headers, $ccHeader, $msg, null);
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
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: <$email>" . "\r\n";
        // send email
        // mail($to, $msg, $headers, $ccHeader);
        $resp = $this->sendEmail($to, $subject, $headers, $ccHeader, $msg, $dataQuote['img']);
        return $resp;
    }
}
