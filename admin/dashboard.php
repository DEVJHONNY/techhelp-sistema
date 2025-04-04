<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Estatísticas gerais
$stats = [
    'total_orcamentos' => $db->query("SELECT COUNT(*) FROM orcamentos")->fetchColumn(),
    'pendentes' => $db->query("SELECT COUNT(*) FROM orcamentos WHERE status = 'pendente'")->fetchColumn(),
    'concluidos' => $db->query("SELECT COUNT(*) FROM orcamentos WHERE status = 'concluido'")->fetchColumn(),
    'faturamento' => $db->query("SELECT SUM(valor_orcamento) FROM orcamentos WHERE status = 'concluido'")->fetchColumn()
];

// Dados para gráficos
$servicos = $db->query("
    SELECT servico_solicitado, COUNT(*) as total 
    FROM orcamentos 
    GROUP BY servico_solicitado
")->fetchAll(PDO::FETCH_ASSOC);

$ultimos_meses = $db->query("
    SELECT DATE_FORMAT(data_solicitacao, '%Y-%m') as mes, COUNT(*) as total 
    FROM orcamentos 
    GROUP BY mes 
    ORDER BY mes DESC 
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - TechHelp</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin">
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <h1>Dashboard</h1>
            <a href="logout.php" class="btn-logout">Sair</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="dashboard-grid">
            <!-- Cards de Estatísticas -->
            <div class="stat-card total">
                <i class="fas fa-file-invoice"></i>
                <div class="stat-info">
                    <span class="stat-value"><?php echo $stats['total_orcamentos']; ?></span>
                    <span class="stat-label">Orçamentos Totais</span>
                </div>
            </div>
            
            <div class="stat-card pending">
                <i class="fas fa-clock"></i>
                <div class="stat-info">
                    <span class="stat-value"><?php echo $stats['pendentes']; ?></span>
                    <span class="stat-label">Pendentes</span>
                </div>
            </div>
            
            <div class="stat-card approved">
                <i class="fas fa-check-circle"></i>
                <div class="stat-info">
                    <span class="stat-value"><?php echo $stats['concluidos']; ?></span>
                    <span class="stat-label">Concluídos</span>
                </div>
            </div>
            
            <div class="stat-card revenue">
                <i class="fas fa-dollar-sign"></i>
                <div class="stat-info">
                    <span class="stat-value">R$ <?php echo number_format($stats['faturamento'], 2, ',', '.'); ?></span>
                    <span class="stat-label">Faturamento</span>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3>Serviços Solicitados</h3>
                <canvas id="servicosChart"></canvas>
            </div>
            
            <div class="chart-card">
                <h3>Orçamentos por Mês</h3>
                <canvas id="mensalChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Serviços
        new Chart(document.getElementById('servicosChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($servicos, 'servico_solicitado')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($servicos, 'total')); ?>,
                    backgroundColor: ['#25D366', '#128C7E', '#075E54']
                }]
            }
        });

        // Gráfico Mensal
        new Chart(document.getElementById('mensalChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($ultimos_meses, 'mes')); ?>,
                datasets: [{
                    label: 'Orçamentos',
                    data: <?php echo json_encode(array_column($ultimos_meses, 'total')); ?>,
                    borderColor: '#25D366',
                    tension: 0.1
                }]
            }
        });
    </script>
</body>
</html>