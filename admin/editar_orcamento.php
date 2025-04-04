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

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();

        // Atualizar cliente
        $stmt = $db->prepare("
            UPDATE clientes 
            SET nome = ?, email = ?, telefone = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['nome'],
            $_POST['email'],
            $_POST['telefone'],
            $orcamento['id_cliente']
        ]);

        // Atualizar orçamento
        $stmt = $db->prepare("
            UPDATE orcamentos 
            SET servico_solicitado = ?, 
                descricao_problema = ?,
                status = ?,
                valor_orcamento = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['servico'],
            $_POST['descricao'],
            $_POST['status'],
            $_POST['valor_orcamento'],
            $_GET['id']
        ]);

        $db->commit();
        header("Location: ver_orcamento.php?id=" . $_GET['id'] . "&updated=1");
        exit;

    } catch(Exception $e) {
        $db->rollBack();
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Orçamento - Painel Administrativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin">
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <h1>Editar Orçamento</h1>
            <div>
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="edit-form">
            <div class="form-grid">
                <div class="form-section">
                    <h2>Dados do Cliente</h2>
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" value="<?php echo $orcamento['nome']; ?>" required autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $orcamento['email']; ?>" autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="tel" name="telefone" value="<?php echo $orcamento['telefone']; ?>" required autocomplete="tel">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Dados do Serviço</h2>
                    <div class="form-group">
                        <label>Serviço Solicitado</label>
                        <select name="servico" required>
                            <option value="notebook" <?php echo $orcamento['servico_solicitado'] == 'notebook' ? 'selected' : ''; ?>>
                                Manutenção de Notebook
                            </option>
                            <option value="pc" <?php echo $orcamento['servico_solicitado'] == 'pc' ? 'selected' : ''; ?>>
                                Manutenção de PC
                            </option>
                            <option value="rede" <?php echo $orcamento['servico_solicitado'] == 'rede' ? 'selected' : ''; ?>>
                                Serviços de Rede
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descrição do Problema</label>
                        <textarea name="descricao" rows="5" required><?php echo $orcamento['descricao_problema']; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Status e Valor</h2>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="pendente" <?php echo $orcamento['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="aprovado" <?php echo $orcamento['status'] == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                            <option value="rejeitado" <?php echo $orcamento['status'] == 'rejeitado' ? 'selected' : ''; ?>>Rejeitado</option>
                            <option value="concluido" <?php echo $orcamento['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Valor do Orçamento</label>
                        <input type="number" name="valor_orcamento" step="0.01" value="<?php echo $orcamento['valor_orcamento']; ?>">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-salvar">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="ver_orcamento.php?id=<?php echo $_GET['id']; ?>" class="btn-cancelar">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>
