<?php
session_start();

if(isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Usuário ou senha inválidos";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Área Administrativa</title>
    <!-- Corrigir caminhos relativos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login">
    <div class="login-container">
        <a href="../" class="logo-link">
            <i class="fas fa-laptop-medical"></i>
            <span>TechHelp</span>
        </a>
        
        <h1><i class="fas fa-lock"></i> Área Restrita</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Usuário
                </label>
                <input type="text" id="username" name="username" required autofocus autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-key"></i> Senha
                </label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>
        
        <a href="../" class="voltar-site">
            <i class="fas fa-arrow-left"></i> Voltar para o site
        </a>
    </div>
</body>
</html>
