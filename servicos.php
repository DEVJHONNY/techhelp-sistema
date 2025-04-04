<?php 
$pagina = 'servicos';
$titulo = "Assistência Técnica";
$pagina_css = "servicos";
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Serviços de Assistência Técnica</h1>
        <p>Soluções profissionais para seus equipamentos</p>
    </div>
</section>

<section class="servicos-page">
    <div class="container">
        <div class="servicos-grid">
            <!-- Manutenção de Notebooks -->
            <div class="servico-card">
                <i class="fas fa-laptop"></i>
                <h3>Notebooks</h3>
                <ul class="servico-lista">
                    <li>Formatação e instalação de sistemas</li>
                    <li>Troca de tela e teclado</li>
                    <li>Reparo em placa-mãe</li>
                    <li>Upgrade de memória e SSD</li>
                </ul>
                <a href="contato.php?servico=notebook" class="btn-orcamento">Solicitar Orçamento</a>
            </div>

            <!-- Manutenção de PCs -->
            <div class="servico-card">
                <i class="fas fa-desktop"></i>
                <h3>Computadores</h3>
                <ul class="servico-lista">
                    <li>Montagem de PC</li>
                    <li>Limpeza e manutenção</li>
                    <li>Recuperação de dados</li>
                    <li>Instalação de programas</li>
                </ul>
                <a href="contato.php?servico=pc" class="btn-orcamento">Solicitar Orçamento</a>
            </div>

            <!-- Redes -->
            <div class="servico-card">
                <i class="fas fa-network-wired"></i>
                <h3>Redes</h3>
                <ul class="servico-lista">
                    <li>Instalação de redes</li>
                    <li>Configuração de roteadores</li>
                    <li>Cabeamento estruturado</li>
                    <li>Redes empresariais</li>
                </ul>
                <a href="contato.php?servico=rede" class="btn-orcamento">Solicitar Orçamento</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
