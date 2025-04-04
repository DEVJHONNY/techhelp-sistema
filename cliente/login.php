<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = (new Database())->getConnection();
        
        // Primeiro verifica se existe um orçamento com esses dados
        $stmt = $db->prepare("
            SELECT c.* 
            FROM clientes c 
            INNER JOIN orcamentos o ON c.id = o.id_cliente 
            WHERE c.email = ? AND c.telefone = ? 
            LIMIT 1
        ");
        
        $stmt->execute([
            $_POST['email'],
            $_POST['telefone']
        ]);
        
        if ($cliente = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['cliente'] = $cliente;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Nenhum orçamento encontrado com estes dados. Por favor, solicite um orçamento primeiro.";
        }
    } catch (Exception $e) {
        $error = "Erro ao acessar o sistema. Tente novamente.";
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Área do Cliente - Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="manifest" href="../manifest.json">
</head>
<body class="cliente-login">
    <div class="login-container">
        <a href="../" class="logo-link">
            <i class="fas fa-laptop-medical"></i>
            <span>TechHelp</span>
        </a>
        
        <h1><i class="fas fa-lock"></i> Área do Cliente</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" id="email" name="email" required autofocus
                       autocomplete="email" placeholder="exemplo@email.com">
            </div>
            
            <div class="form-group">
                <label for="telefone">
                    <i class="fas fa-phone"></i> Telefone
                </label>
                <input type="tel" id="telefone" name="telefone" required
                       autocomplete="tel" placeholder="(71) 99999-9999">
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>
        
        <div class="login-help">
            <p>Ainda não tem orçamento?</p>
            <a href="../contato.php" class="btn-orcamento">
                <i class="fas fa-file-invoice-dollar"></i>
                Solicitar Orçamento
            </a>
        </div>

        <a href="../" class="voltar-site">
            <i class="fas fa-arrow-left"></i> Voltar para o site
        </a>
    </div>

    <script>
    const telefoneInput = document.getElementById('telefone');
    
    function formatarTelefone(value) {
        value = value.replace(/\D/g, '');
        value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        return value;
    }

    telefoneInput.addEventListener('input', (e) => {
        let value = e.target.value;
        e.target.value = formatarTelefone(value);
    });

    document.querySelector('.login-form').addEventListener('submit', function(e) {
        const telefone = telefoneInput.value;
        if (!/^\(\d{2}\)\s\d{5}-\d{4}$/.test(telefone)) {
            e.preventDefault();
            alert('Digite o telefone no formato correto: (71) 99999-9999');
            telefoneInput.focus();
        }
    });
    </script>
</body>
</html>
