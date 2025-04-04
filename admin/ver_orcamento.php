<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if(isset($_GET['id'])) {
    $stmt = $db->prepare("
        SELECT o.*, c.nome, c.email, c.telefone 
        FROM orcamentos o 
        JOIN clientes c ON o.id_cliente = c.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Processar atualização de status
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $stmt = $db->prepare("
        UPDATE orcamentos 
        SET status = ?, valor_orcamento = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['status'],
        $_POST['valor_orcamento'],
        $_GET['id']
    ]);
    header("Location: ver_orcamento.php?id=" . $_GET['id'] . "&updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Orçamento - Painel Administrativo</title>
    <!-- Corrigir caminhos relativos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin">
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <h1>Detalhes do Orçamento</h1>
            <div>
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <?php if(isset($_GET['updated'])): ?>
            <div class="alert alert-success">Orçamento atualizado com sucesso!</div>
        <?php endif; ?>

        <div class="orcamento-details">
            <div class="orcamento-card cliente-info">
                <h2>Informações do Cliente</h2>
                <p><strong>Nome:</strong> <?php echo $orcamento['nome']; ?></p>
                <p><strong>Email:</strong> <?php echo $orcamento['email']; ?></p>
                <p><strong>Telefone:</strong> <?php echo $orcamento['telefone']; ?></p>
                <a href="https://wa.me/55<?php echo preg_replace('/\D/', '', $orcamento['telefone']); ?>" 
                   class="btn-whatsapp" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp"></i> Enviar WhatsApp
                </a>
            </div>

            <div class="orcamento-card servico-info">
                <h2>Detalhes do Serviço</h2>
                <p><strong>Serviço:</strong> <?php echo $orcamento['servico_solicitado']; ?></p>
                <p><strong>Descrição:</strong></p>
                <div class="descricao-problema">
                    <?php echo nl2br($orcamento['descricao_problema']); ?>
                </div>
                <p><strong>Data da Solicitação:</strong> 
                    <?php echo date('d/m/Y H:i', strtotime($orcamento['data_solicitacao'])); ?>
                </p>
            </div>

            <div class="orcamento-card status-info">
                <h2>Gerenciar Status</h2>
                <form method="POST" class="status-form">
                    <div class="form-group">
                        <label>Status do Orçamento</label>
                        <select name="status" required>
                            <option value="pendente" <?php echo $orcamento['status'] == 'pendente' ? 'selected' : ''; ?>>
                                Pendente
                            </option>
                            <option value="aprovado" <?php echo $orcamento['status'] == 'aprovado' ? 'selected' : ''; ?>>
                                Aprovado
                            </option>
                            <option value="rejeitado" <?php echo $orcamento['status'] == 'rejeitado' ? 'selected' : ''; ?>>
                                Rejeitado
                            </option>
                            <option value="concluido" <?php echo $orcamento['status'] == 'concluido' ? 'selected' : ''; ?>>
                                Concluído
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Valor do Orçamento</label>
                        <input type="number" name="valor_orcamento" step="0.01" 
                               value="<?php echo $orcamento['valor_orcamento']; ?>">
                    </div>
                    <button type="submit" class="btn-atualizar">Atualizar Status</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
