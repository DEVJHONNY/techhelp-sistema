<?php
require_once 'config/database.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarNotificacaoEmail($dados) {
    $config = require 'config/email.php';
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['port'];
        $mail->CharSet = 'UTF-8';

        // Email para o administrador
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($config['username']); // admin
        $mail->addReplyTo($dados['email'], $dados['nome']);

        // Template do admin
        $adminTemplate = file_get_contents('templates/email.html');
        $adminReplacements = [
            '{NOME}' => $dados['nome'],
            '{EMAIL}' => $dados['email'],
            '{TELEFONE}' => $dados['telefone'],
            '{SERVICO}' => $dados['servico'],
            '{MENSAGEM}' => nl2br($dados['mensagem']),
            '{DATA}' => date('d/m/Y H:i:s'),
            '{IP}' => $_SERVER['REMOTE_ADDR']
        ];
        
        $adminBody = str_replace(array_keys($adminReplacements), array_values($adminReplacements), $adminTemplate);

        $mail->isHTML(true);
        $mail->Subject = 'Novo Orçamento Recebido - TechHelp';
        $mail->Body = $adminBody;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $adminBody));

        $mail->send();

        // Limpar os destinatários para o novo email
        $mail->clearAddresses();
        $mail->clearReplyTos();

        // Email para o cliente
        $mail->addAddress($dados['email'], $dados['nome']);
        $mail->addReplyTo($config['from_email'], $config['from_name']);

        // Template do cliente
        $clienteTemplate = file_get_contents('templates/email_cliente.html');
        $clienteReplacements = [
            '{NOME}' => $dados['nome'],
            '{SERVICO}' => $dados['servico'],
            '{MENSAGEM}' => urlencode($dados['mensagem']), // Codificar para URL
            '{DATA}' => date('d/m/Y H:i:s')
        ];
        
        $clienteBody = str_replace(array_keys($clienteReplacements), array_values($clienteReplacements), $clienteTemplate);

        $mail->Subject = 'Recebemos seu Orçamento - TechHelp';
        $mail->Body = $clienteBody;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $clienteBody));

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro no envio de email: " . $mail->ErrorInfo);
        return false;
    }
}

function enviarNotificacaoWhatsApp($dados) {
    $telefone = '5571992124952';
    $mensagem = urlencode("🔔 *Novo Orçamento*\n\n" .
        "*Cliente:* " . $dados['nome'] . "\n" .
        "*Telefone:* " . $dados['telefone'] . "\n" .
        "*Serviço:* " . $dados['servico'] . "\n" .
        "*Mensagem:* " . $dados['mensagem']);

    return "https://wa.me/$telefone?text=$mensagem";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!$db) {
            throw new Exception("Erro na conexão com o banco de dados");
        }

        $db->beginTransaction();

        // Inserir cliente
        $stmt = $db->prepare("
            INSERT INTO clientes (nome, email, telefone)
            VALUES (:nome, :email, :telefone)
        ");

        $stmt->execute([
            ':nome' => $_POST['nome'],
            ':email' => $_POST['email'],
            ':telefone' => $_POST['telefone']
        ]);

        $id_cliente = $db->lastInsertId();

        // Inserir orçamento
        $stmt = $db->prepare("
            INSERT INTO orcamentos (id_cliente, servico_solicitado, descricao_problema)
            VALUES (:id_cliente, :servico, :mensagem)
        ");

        $stmt->execute([
            ':id_cliente' => $id_cliente,
            ':servico' => $_POST['servico'],
            ':mensagem' => $_POST['mensagem']
        ]);

        $db->commit();

        // Enviar emails
        $emailEnviado = enviarNotificacaoEmail($_POST);
        
        if ($emailEnviado) {
            header("Location: contato.php?status=success");
        } else {
            throw new Exception("Erro ao enviar email de notificação");
        }
        exit;

    } catch(Exception $e) {
        if (isset($db)) {
            $db->rollBack();
        }
        error_log("Erro no processamento do contato: " . $e->getMessage());
        header("Location: contato.php?status=error&message=" . urlencode($e->getMessage()));
        exit;
    }
}
