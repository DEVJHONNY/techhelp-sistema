<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$db = (new Database())->getConnection();

// Buscar dados para o relatório
$periodo = $_GET['periodo'] ?? 'mes';
$where = '';

switch($periodo) {
    case 'mes':
        $where = "WHERE MONTH(o.data_solicitacao) = MONTH(CURRENT_DATE())";
        break;
    case 'semana':
        $where = "WHERE o.data_solicitacao >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)";
        break;
}

$query = "
    SELECT 
        o.*, 
        c.nome, 
        c.email, 
        c.telefone,
        (SELECT AVG(nota) FROM avaliacoes WHERE id_orcamento = o.id) as avaliacao
    FROM orcamentos o
    JOIN clientes c ON o.id_cliente = c.id
    $where
    ORDER BY o.data_solicitacao DESC
";

$orcamentos = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Gerar HTML do relatório
$html = '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .status { padding: 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Orçamentos - TechHelp</h1>
        <p>Período: ' . ucfirst($periodo) . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Status</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>';

foreach($orcamentos as $o) {
    $html .= '
        <tr>
            <td>' . date('d/m/Y', strtotime($o['data_solicitacao'])) . '</td>
            <td>' . $o['nome'] . '</td>
            <td>' . $o['servico_solicitado'] . '</td>
            <td>' . ucfirst($o['status']) . '</td>
            <td>R$ ' . number_format($o['valor_orcamento'], 2, ',', '.') . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// Gerar PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio-orcamentos.pdf", ["Attachment" => false]);
