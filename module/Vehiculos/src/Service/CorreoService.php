<?php

namespace Vehiculos\Service;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;

class CorreoService
{
    private array $smtpConfig;

    public function __construct(array $smtpConfig = [])
    {
        $this->smtpConfig = $smtpConfig;
    }

    /**
     * Enviar código de confirmación
     */
    public function enviarCodigoConfirmacion(string $destinatario, string $codigo): bool
    {
        $asunto = 'Código de Confirmación - Sistema QR Vehículos';
        $anio = date('Y');
        $siteUrl = $this->smtpConfig['site_url'] ?? 'www.aprotec.cl';

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
            <p>APROTEC</p>
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
            <p>&copy; {$anio} APROTEC</p>
            <p>{$siteUrl}/Vehiculos</p>
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
        $fromEmail = $this->smtpConfig['from_email'] ?? 'noreply@aprotec.cl';
        $fromName = $this->smtpConfig['from_name'] ?? 'Sistema QR Vehículos - APROTEC';

        try {
            $message = new Message();
            $message->setFrom($fromEmail, $fromName)
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

            // Configurar SMTP desde config inyectada
            $smtpHost = $this->smtpConfig['host'] ?? '';
            $smtpPort = (int) ($this->smtpConfig['port'] ?? 587);
            $smtpUser = $this->smtpConfig['username'] ?? '';
            $smtpPass = $this->smtpConfig['password'] ?? '';
            $smtpSsl  = $this->smtpConfig['ssl'] ?? 'tls';

            if (empty($smtpHost) || empty($smtpUser)) {
                error_log("SMTP no configurado. Correo no enviado a: {$destinatario}");
                return false;
            }

            $options = new SmtpOptions([
                'name' => $smtpHost,
                'host' => $smtpHost,
                'port' => $smtpPort,
                'connection_class' => 'plain',
                'connection_config' => [
                    'username' => $smtpUser,
                    'password' => $smtpPass,
                    'ssl' => $smtpSsl,
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
