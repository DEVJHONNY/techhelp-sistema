<?php 
$titulo = "Home";
$pagina = 'home';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>

<section id="inicio" class="hero">
    <div class="hero-bg"></div> <!-- Novo elemento para background -->
    <div class="container">
        <h1 class="animate-fade-up">Assistência Técnica Especializada</h1>
        <p class="animate-fade-up" style="animation-delay: 0.2s">
            Resolva seus problemas tecnológicos com quem entende
        </p>
        <div class="cta-buttons animate-fade-up" style="animation-delay: 0.4s">
            <a href="contato.php" class="cta-button">
                <i class="fas fa-file-invoice"></i> Solicitar Orçamento
            </a>
            <a href="https://wa.me/5571992124952" class="cta-button whatsapp" 
               target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
