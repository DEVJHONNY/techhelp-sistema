<?php
require_once 'config/database.php';
require_once 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

function enviarNotificacao($cliente_id, $tipo, $mensagem) {
    $database = new Database();
    $db = $database->getConnection();

    // Buscar dados do cliente
    $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        return false;
    }

    // Email
    if ($tipo == 'email') {
        require_once 'config/email.php';
        $mail = new PHPMailer(true);

        try {
            $config = require 'config/email.php';
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($cliente['email'], $cliente['nome']);
            $mail->isHTML(true);
            $mail->Subject = 'Notificação - TechHelp';
            $mail->Body = $mensagem;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $mail->ErrorInfo);
            return false;
        }
    }

    // WhatsApp
    if ($tipo == 'whatsapp') {
        $telefone = preg_replace('/\D/', '', $cliente['telefone']);
        $mensagem = urlencode($mensagem);
        $url = "https://wa.me/55$telefone?text=$mensagem";
        header("Location: $url");
        return true;
    }

    // Push Notification
    if ($tipo == 'push') {
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:example@yourdomain.org',
                'publicKey' => 'YOUR_PUBLIC_KEY',
                'privateKey' => 'YOUR_PRIVATE_KEY',
            ],
        ];

        $webPush = new WebPush($auth);

        $subscription = Subscription::create([
            'endpoint' => $cliente['push_endpoint'],
            'keys' => [
                'p256dh' => $cliente['push_p256dh'],
                'auth' => $cliente['push_auth'],
            ],
        ]);

        $report = $webPush->sendOneNotification($subscription, $mensagem);

        if ($report->isSuccess()) {
            return true;
        } else {
            error_log("Erro ao enviar push notification: " . $report->getReason());
            return false;
        }
    }

    return false;
}
?>