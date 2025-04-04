<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$db = (new Database())->getConnection();

if (isset($_GET['orcamento'])) {
    $stmt = $db->prepare("
        SELECT st.*, o.status as status_geral
        FROM status_tracking st
        JOIN orcamentos o ON st.id_orcamento = o.id
        WHERE st.id_orcamento = ?
        ORDER BY st.data_atualizacao DESC
    ");
    
    $stmt->execute([$_GET['orcamento']]);
    $tracking = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'tracking' => $tracking
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'ID do orçamento não fornecido'
    ]);
}
