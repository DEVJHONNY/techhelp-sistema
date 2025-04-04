<?php 
$pagina = 'sobre';
$titulo = "Sobre Mim";
$pagina_css = "sobre";
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Sobre Mim</h1>
        <p>Conheça mais sobre minha experiência e qualificações</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="perfil-container">
            <div class="perfil-foto">
                <img src="assets/images/perfil.jpg" alt="Foto do Técnico" class="animate-fade-up">
            </div>
            <div class="perfil-info animate-fade-up" style="animation-delay: 0.2s">
                <h2>Técnico em Tecnologia</h2>
                <p>Formado em técnico de tecnologia, com experiência em manutenção...</p>
                
                <div class="certificacoes">
                    <h3>Certificações</h3>
                    <div class="grid-container">
                        <!-- Cards de certificações aqui -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
