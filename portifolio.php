<?php 
$pagina = 'portfolio';
$titulo = "Portfólio";
$pagina_css = "portfolio";
require_once 'includes/header.php';
require_once 'includes/nav.php';

// Buscar trabalhos do portfólio
require_once 'config/database.php';
$db = (new Database())->getConnection();

try {
    $portfolio = $db->query("
        SELECT p.*, 
               COALESCE(AVG(a.nota), 0) as media_avaliacao,
               COUNT(DISTINCT a.id) as total_avaliacoes
        FROM portfolio p
        LEFT JOIN avaliacoes a ON p.id = a.id_portfolio
        GROUP BY p.id
        ORDER BY p.data_publicacao DESC
    ")->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar portfólio: " . $e->getMessage());
    $portfolio = [];
}
?>

<section class="page-header">
    <div class="container">
        <h1>Portfólio de Trabalhos</h1>
        <p>Conheça alguns dos nossos serviços realizados</p>
    </div>
</section>

<section class="portfolio-page">
    <div class="container">
        <!-- Filtros com contador -->
        <div class="portfolio-filters">
            <button class="filter-btn active" data-filter="todos">
                Todos <span class="count"><?php echo count($portfolio); ?></span>
            </button>
            <?php
            $categorias = array_count_values(array_column($portfolio, 'categoria'));
            foreach ($categorias as $categoria => $total): ?>
                <button class="filter-btn" data-filter="<?php echo $categoria; ?>">
                    <?php echo ucfirst($categoria); ?> <span class="count"><?php echo $total; ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="portfolio-grid">
            <?php foreach($portfolio as $item): ?>
            <div class="portfolio-item animate-fade-up" 
                 data-categoria="<?php echo htmlspecialchars($item['categoria']); ?>">
                <img src="<?php echo htmlspecialchars($item['imagem']); ?>" 
                     alt="<?php echo htmlspecialchars($item['titulo']); ?>"
                     loading="lazy">
                <div class="portfolio-info">
                    <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($item['descricao']); ?></p>
                    
                    <?php if($item['media_avaliacao'] > 0): ?>
                    <div class="avaliacao">
                        <div class="estrelas">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $item['media_avaliacao'] ? 'active' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <small><?php echo $item['total_avaliacoes']; ?> avaliações</small>
                    </div>
                    <?php endif; ?>

                    <?php if($item['depoimento']): ?>
                    <div class="depoimento">
                        <i class="fas fa-quote-left"></i>
                        <p><?php echo htmlspecialchars($item['depoimento']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if(empty($portfolio)): ?>
        <div class="no-results">
            <i class="fas fa-folder-open"></i>
            <p>Nenhum trabalho encontrado.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox -->
<div class="lightbox" role="dialog" aria-modal="true">
    <button class="close-lightbox" aria-label="Fechar">&times;</button>
    <img src="" alt="" loading="lazy">
    <div class="lightbox-caption"></div>
</div>

<?php require_once 'includes/footer.php'; ?>

<!-- Scripts específicos do portfólio -->
<script src="assets/js/gallery.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    const lightbox = document.querySelector('.lightbox');
    const lightboxImg = lightbox.querySelector('img');
    const lightboxCaption = lightbox.querySelector('.lightbox-caption');
    const closeLightbox = document.querySelector('.close-lightbox');
    
    // Função para fechar lightbox
    function closeLightboxHandler() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            lightboxImg.src = '';
            lightboxCaption.textContent = '';
        }, 300);
    }

    // Abrir lightbox
    portfolioItems.forEach(item => {
        const img = item.querySelector('img');
        const title = item.querySelector('h3')?.textContent;
        
        img.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt;
            if (title) lightboxCaption.textContent = title;
            
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    // Fechar lightbox
    closeLightbox.addEventListener('click', closeLightboxHandler);
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightboxHandler();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            closeLightboxHandler();
        }
    });
});
</script>
</body>
</html>
