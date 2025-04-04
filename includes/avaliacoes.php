<?php
function calcularMediaAvaliacao($orcamento_id) {
    global $db;
    $stmt = $db->prepare("
        SELECT AVG(nota) as media 
        FROM avaliacoes 
        WHERE id_orcamento = ?
    ");
    $stmt->execute([$orcamento_id]);
    return $stmt->fetch()['media'] ?? 0;
}
