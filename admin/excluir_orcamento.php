<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

if(isset($_GET['id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Começar transação
        $db->beginTransaction();
        
        // Pegar id_cliente antes de excluir o orçamento
        $stmt = $db->prepare("SELECT id_cliente FROM orcamentos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Excluir orçamento
        $stmt = $db->prepare("DELETE FROM orcamentos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        // Verificar se cliente tem outros orçamentos
        $stmt = $db->prepare("SELECT COUNT(*) FROM orcamentos WHERE id_cliente = ?");
        $stmt->execute([$orcamento['id_cliente']]);
        $count = $stmt->fetchColumn();
        
        // Se não tiver outros orçamentos, excluir cliente
        if($count == 0) {
            $stmt = $db->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([$orcamento['id_cliente']]);
        }
        
        $db->commit();
        header("Location: index.php?status=deleted");
        
    } catch(Exception $e) {
        $db->rollBack();
        header("Location: index.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: index.php");
}
exit;
