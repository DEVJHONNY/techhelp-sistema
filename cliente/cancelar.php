<?php
session_start();
if (!isset($_SESSION['cliente'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

if (isset($_GET['id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Verificar se o orçamento existe e pertence ao cliente
        $stmt = $db->prepare("
            SELECT * FROM orcamentos 
            WHERE id = ? AND id_cliente = ?
        ");
        $stmt->execute([$_GET['id'], $_SESSION['cliente']['id']]);
        
        if ($orcamento = $stmt->fetch()) {
            // Atualizar para rejeitado
            $stmt = $db->prepare("
                UPDATE orcamentos 
                SET status = 'rejeitado' 
                WHERE id = ?
            ");
            
            if ($stmt->execute([$_GET['id']])) {
                // Se atualizou com sucesso
                header('Location: dashboard.php?status=cancelado');
            } else {
                // Se falhou a atualização
                header('Location: dashboard.php?error=1');
            }
        } else {
            // Se não encontrou o orçamento
            header('Location: dashboard.php?error=2');
        }
    } catch (Exception $e) {
        // Log do erro para debug
        error_log("Erro ao cancelar orçamento: " . $e->getMessage());
        header('Location: dashboard.php?error=3');
    }
    exit;
}

// Se não tem ID
header('Location: dashboard.php');
exit;
