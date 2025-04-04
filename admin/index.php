<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Construir query com filtros
$where = [];
$params = [];

if(!empty($_GET['search'])) {
    $search = "%{$_GET['search']}%";
    $where[] = "(c.nome LIKE ? OR c.email LIKE ? OR c.telefone LIKE ?)";
    $params = array_merge($params, [$search, $search, $search]);
}

if(!empty($_GET['status'])) {
    $where[] = "o.status = ?";
    $params[] = $_GET['status'];
}

if(!empty($_GET['servico'])) {
    $where[] = "o.servico_solicitado = ?";
    $params[] = $_GET['servico'];
}

// Montar WHERE clause
$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Query principal
$query = "
    SELECT o.*, c.nome, c.email, c.telefone 
    FROM orcamentos o 
    JOIN clientes c ON o.id_cliente = c.id 
    {$whereClause}
    ORDER BY o.data_solicitacao DESC
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug para ver a query executada
if(isset($_GET['debug'])) {
    echo "<pre>";
    print_r([
        'query' => $query,
        'params' => $params,
        'total' => count($orcamentos)
    ]);
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <!-- Corrigir caminhos relativos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin">
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <h1>Painel Administrativo</h1>
            <a href="logout.php" class="btn-logout">Sair</a>
        </div>
    </nav>

    <div class="admin-container">
        <h2>Orçamentos Recebidos</h2>
        
        <div class="filters-section">
            <form class="search-form" method="GET">
                <div class="search-group">
                    <input type="text" name="search" 
                           placeholder="Buscar por nome, email ou telefone..." 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <select name="status" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="pendente" <?php echo isset($_GET['status']) && $_GET['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="aprovado" <?php echo isset($_GET['status']) && $_GET['status'] == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                    <option value="rejeitado" <?php echo isset($_GET['status']) && $_GET['status'] == 'rejeitado' ? 'selected' : ''; ?>>Rejeitado</option>
                    <option value="concluido" <?php echo isset($_GET['status']) && $_GET['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                </select>
                
                <select name="servico" onchange="this.form.submit()">
                    <option value="">Todos os Serviços</option>
                    <option value="notebook" <?php echo isset($_GET['servico']) && $_GET['servico'] == 'notebook' ? 'selected' : ''; ?>>Notebook</option>
                    <option value="pc" <?php echo isset($_GET['servico']) && $_GET['servico'] == 'pc' ? 'selected' : ''; ?>>PC</option>
                    <option value="rede" <?php echo isset($_GET['servico']) && $_GET['servico'] == 'rede' ? 'selected' : ''; ?>>Rede</option>
                </select>
                
                <a href="index.php" class="btn-clear">
                    <i class="fas fa-times"></i> Limpar Filtros
                </a>
            </form>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Contato</th>
                    <th>Serviço</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orcamentos as $orcamento): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($orcamento['data_solicitacao'])); ?></td>
                        <td><?php echo $orcamento['nome']; ?></td>
                        <td>
                            <?php echo $orcamento['telefone']; ?><br>
                            <?php echo $orcamento['email']; ?>
                        </td>
                        <td><?php echo $orcamento['servico_solicitado']; ?></td>
                        <td>
                            <span class="status-<?php echo $orcamento['status']; ?>">
                                <?php echo ucfirst($orcamento['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="ver_orcamento.php?id=<?php echo $orcamento['id']; ?>" class="btn-view" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="editar_orcamento.php?id=<?php echo $orcamento['id']; ?>" class="btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" onclick="confirmarExclusao(<?php echo $orcamento['id']; ?>)" class="btn-delete" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este orçamento?')) {
        window.location.href = 'excluir_orcamento.php?id=' + id;
    }
}
</script>
</body>
</html>
