<footer>
    <div class="container footer-content">
        <div class="footer-info">
            <h3 style="color: white !important;">TechHelp</h3>
            <p>Assistência Técnica Especializada</p>
        </div>
        <div class="footer-social">
            <h3 style="color: white !important;">Redes Sociais</h3>
            <div class="social-icons">
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-admin">
            <a href="/sistema/admin/login.php" class="admin-link">
                <i class="fas fa-lock"></i> Área Administrativa
            </a>
        </div>
    </div>
    
    <!-- Mapa do Site -->
    <div class="sitemap">
        <div class="container">
            <div class="sitemap-grid">
                <div class="sitemap-section">
                    <h4>Serviços</h4>
                    <ul>
                        <li><a href="servicos.php#notebooks">Notebooks</a></li>
                        <li><a href="servicos.php#computadores">Computadores</a></li>
                        <li><a href="servicos.php#redes">Redes</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h4>Institucional</h4>
                    <ul>
                        <li><a href="sobre.php">Sobre Mim</a></li>
                        <li><a href="portifolio.php">Portfólio</a></li>
                        <li><a href="contato.php">Orçamento</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h4>Contato</h4>
                    <ul>
                        <li><i class="fab fa-whatsapp"></i> (71) 99212-4952</li>
                        <li><i class="far fa-envelope"></i> lucas.rocha.11@hotmail.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> Salvador - BA</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- LGPD Banner -->
    <div id="lgpd-banner" class="lgpd-banner">
        <p>Utilizamos cookies para melhorar sua experiência. Ao continuar navegando, você concorda com nossa <a href="privacidade.php">Política de Privacidade</a>.</p>
        <button id="accept-cookies">Aceitar</button>
    </div>
    
    <div class="container">
        <p>&copy; 2024 TechHelp - Todos os direitos reservados</p>
    </div>
</footer>

<!-- Botão Voltar ao Topo -->
<button class="voltar-topo" aria-label="Voltar ao topo">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Botão de Compartilhamento -->
<button class="share-button" onclick="compartilharSite()">
    <i class="fas fa-share-alt"></i> Compartilhar
</button>

<!-- Scripts -->
<script src="/sistema/assets/js/main.js"></script>
<?php require_once 'includes/chatbot.php'; ?>

<script>
function compartilharSite() {
    if (navigator.share) {
        navigator.share({
            title: 'TechHelp',
            text: 'Assistência técnica especializada em Salvador',
            url: window.location.href
        });
    }
}
</script>
</body>
</html>
