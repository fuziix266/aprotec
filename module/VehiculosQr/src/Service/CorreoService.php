<?php

namespace VehiculosQr\Service;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;

class CorreoService
{
    private string $fromEmail = 'noreply@municipalidadarica.cl';
    private string $fromName = 'Sistema QR Vehículos - DIDECO Arica';

    /**
     * Enviar código de confirmación
     */
    public function enviarCodigoConfirmacion(string $destinatario, string $codigo): bool
    {
        $asunto = 'Código de Confirmación - Sistema QR Vehículos';
        
        $mensaje = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0d47a1; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f5f5f5; }
        .codigo { font-size: 32px; font-weight: bold; color: #0d47a1; text-align: center; padding: 20px; background: white; margin: 20px 0; letter-spacing: 5px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistema QR Vehículos Municipales</h1>
            <p>Municipalidad de Arica - DIDECO</p>
        </div>
        <div class="content">
            <h2>Código de Confirmación</h2>
            <p>Estimado/a funcionario/a:</p>
            <p>Su código de confirmación para completar el registro de su vehículo es:</p>
            <div class="codigo">{$codigo}</div>
            <p><strong>Este código expira en 30 minutos.</strong></p>
            <p>Si usted no solicitó este código, ignore este mensaje.</p>
        </div>
        <div class="footer">
            <p>© 2025 Municipalidad de Arica - DIDECO</p>
            <p>www.didecoarica.cl/vehiculos</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $this->enviarCorreo($destinatario, $asunto, $mensaje);
    }

    /**
     * Enviar correo genérico
     */
    private function enviarCorreo(string $destinatario, string $asunto, string $cuerpoHtml): bool
    {
        try {
            $message = new Message();
            $message->setFrom($this->fromEmail, $this->fromName)
                    ->addTo($destinatario)
                    ->setSubject($asunto);
            
            $htmlPart = new \Laminas\Mime\Part($cuerpoHtml);
            $htmlPart->type = 'text/html';
            $htmlPart->charset = 'utf-8';
            
            $body = new \Laminas\Mime\Message();
            $body->setParts([$htmlPart]);
            
            $message->setBody($body);
            $message->setEncoding('UTF-8');
            
            $contentTypeHeader = new \Laminas\Mail\Header\ContentType();
            $contentTypeHeader->setType('text/html');
            $contentTypeHeader->addParameter('charset', 'utf-8');
            $message->getHeaders()->addHeader($contentTypeHeader);
            
            // En desarrollo, solo simular envío
            if (getenv('APP_ENV') === 'development' || php_sapi_name() === 'cli-server') {
                error_log("CORREO SIMULADO: {$destinatario} - {$asunto}");
                error_log("Código: " . strip_tags($cuerpoHtml));
                return true;
            }
            
            // Configurar SMTP (actualizar con credenciales reales)
            $options = new SmtpOptions([
                'name' => 'localhost',
                'host' => 'smtp.municipalidadarica.cl',
                'port' => 587,
                'connection_class' => 'plain',
                'connection_config' => [
                    'username' => 'noreply@municipalidadarica.cl',
                    'password' => 'password',
                    'ssl' => 'tls',
                ],
            ]);
            
            $transport = new SmtpTransport($options);
            $transport->send($message);
            
            return true;
        } catch (\Exception $e) {
            error_log("Error al enviar correo: " . $e->getMessage());
            return false;
        }
    }
}
