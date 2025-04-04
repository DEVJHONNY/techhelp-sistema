<?php
// Define $pagina como 'home' se não estiver definido
$pagina = $pagina ?? 'home';
?>
<nav>
    <div class="container nav-container">
        <a href="/sistema" class="logo">
            <i class="fas fa-laptop-medical"></i>
            <span>TechHelp</span>
        </a>

        <button type="button" class="menu-toggle" aria-label="Menu" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="menu">
            <li><a href="/sistema" <?php echo $pagina == 'home' ? 'class="active"' : ''; ?>>
                <i class="fas fa-home"></i> Home
            </a></li>
            <li><a href="/sistema/sobre.php" <?php echo $pagina == 'sobre' ? 'class="active"' : ''; ?>>
                <i class="fas fa-user"></i> Sobre Mim
            </a></li>
            <li><a href="/sistema/servicos.php" <?php echo $pagina == 'servicos' ? 'class="active"' : ''; ?>>
                <i class="fas fa-tools"></i> Assistência
            </a></li>
            <li><a href="/sistema/portifolio.php" <?php echo $pagina == 'portfolio' ? 'class="active"' : ''; ?>>
                <i class="fas fa-images"></i> Portfólio
            </a></li>
            <li><a href="/sistema/contato.php" <?php echo $pagina == 'contato' ? 'class="active"' : ''; ?>>
                <i class="fas fa-file-invoice-dollar"></i> Orçamento
            </a></li>
            <li><a href="/sistema/cliente/login.php" class="area-cliente">
                <i class="fas fa-user-circle"></i> Área do Cliente
            </a></li>
        </ul>
    </div>
</nav>
