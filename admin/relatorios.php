<?php
require_once '../config/database.php';

function gerarRelatorioFinanceiro($periodo = 'mes') {
    $db = (new Database())->getConnection();
    $query = "
        SELECT 
            DATE(data_solicitacao) as data,
            COUNT(*) as total_servicos,
            SUM(valor_orcamento) as faturamento,
            AVG(valor_orcamento) as ticket_medio
        FROM orcamentos
        WHERE status = 'concluido'
        GROUP BY DATE(data_solicitacao)
        ORDER BY data DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $resultados;
}

function gerarRelatorioClientes($periodo = 'mes') {
    $db = (new Database())->getConnection();
    $query = "
        SELECT 
            c.nome,
            c.email,
            c.telefone,
            COUNT(o.id) as total_orcamentos,
            SUM(o.valor_orcamento) as total_gasto
        FROM clientes c
        JOIN orcamentos o ON c.id = o.id_cliente
        WHERE o.status = 'concluido'
        GROUP BY c.id
        ORDER BY total_gasto DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $resultados;
}

function gerarRelatorioServicos($periodo = 'mes') {
    $db = (new Database())->getConnection();
    $query = "
        SELECT 
            servico_solicitado,
            COUNT(*) as total_servicos,
            SUM(valor_orcamento) as faturamento
        FROM orcamentos
        WHERE status = 'concluido'
        GROUP BY servico_solicitado
        ORDER BY total_servicos DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $resultados;
}
?>