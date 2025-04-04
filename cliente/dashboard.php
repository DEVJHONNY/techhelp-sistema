<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['cliente'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Buscar orçamentos do cliente
$stmt = $db->prepare("
    SELECT * FROM orcamentos 
    WHERE id_cliente = ? 
    ORDER BY data_solicitacao DESC
");
$stmt->execute([$_SESSION['cliente']['id']]);
$orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Área do Cliente - TechHelp</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="cliente-page">
    <nav class="cliente-nav">
        <div class="container nav-container">
            <div class="logo">
                <i class="fas fa-laptop-medical"></i>
                <span>TechHelp</span>
            </div>
            <div class="nav-right">
                <span class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <?php echo $_SESSION['cliente']['nome']; ?>
                </span>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container cliente-container">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'cancelado'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Orçamento rejeitado com sucesso!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php
                switch($_GET['error']) {
                    case '1':
                        echo "Erro ao atualizar o orçamento.";
                        break;
                    case '2':
                        echo "Orçamento não encontrado.";
                        break;
                    case '3':
                        echo "Erro no sistema. Tente novamente.";
                        break;
                    default:
                        echo "Ocorreu um erro. Tente novamente.";
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1>Meus Orçamentos</h1>
            <a href="../contato.php" class="btn-novo">
                <i class="fas fa-plus"></i> Novo Orçamento
            </a>
        </div>

        <div class="orcamentos-grid">
            <?php foreach($orcamentos as $orcamento): ?>
                <div class="orcamento-card">
                    <div class="card-header">
                        <span class="status-badge <?php echo $orcamento['status']; ?>">
                            <i class="fas fa-circle"></i>
                            <?php echo ucfirst($orcamento['status']); ?>
                        </span>
                        <span class="data">
                            <i class="far fa-calendar"></i>
                            <?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h3>
                            <i class="fas <?php 
                                echo $orcamento['servico_solicitado'] == 'notebook' ? 'fa-laptop' : 
                                    ($orcamento['servico_solicitado'] == 'pc' ? 'fa-desktop' : 'fa-network-wired'); 
                            ?>"></i>
                            <?php echo ucfirst($orcamento['servico_solicitado']); ?>
                        </h3>
                        <p class="descricao"><?php echo $orcamento['descricao_problema']; ?></p>
                        
                        <?php if($orcamento['valor_orcamento']): ?>
                            <div class="valor">
                                <i class="fas fa-tag"></i>
                                R$ <?php echo number_format($orcamento['valor_orcamento'], 2, ',', '.'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <?php if($orcamento['status'] == 'concluido' && !isset($orcamento['avaliacao'])): ?>
                            <a href="avaliar.php?id=<?php echo $orcamento['id']; ?>" class="btn-avaliar">
                                <i class="fas fa-star"></i> Avaliar Serviço
                            </a>
                        <?php endif; ?>
                        
                        <?php if($orcamento['status'] == 'pendente' || $orcamento['status'] == 'aprovado'): ?>
                            <a href="cancelar.php?id=<?php echo $orcamento['id']; ?>" class="btn-cancelar" 
                               onclick="return confirm('Tem certeza que deseja cancelar este orçamento? Esta ação não pode ser desfeita.')">
                                <i class="fas fa-times"></i> Cancelar Orçamento
                            </a>
                        <?php endif; ?>
                        
                        <a href="https://wa.me/5571992124952" class="btn-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> Falar no WhatsApp
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Adicionar seções para:
        - Histórico de serviços
        - Pontos de fidelidade
        - Status dos orçamentos atuais
        - Sistema de avaliações
        - Notificações -->
    </div>
</body>
</html>
