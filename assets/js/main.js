document.addEventListener('DOMContentLoaded', function() {
    // Menu Toggle with improved error handling
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu');
    const nav = document.querySelector('nav');

    // Menu Mobile Aprimorado
    if (menuToggle && menu) {
        // Animação suave para itens do menu
        const menuItems = menu.querySelectorAll('li');
        menuItems.forEach((item, index) => {
            item.style.transitionDelay = `${index * 0.1}s`;
        });

        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            menu.classList.toggle('active');
            menuToggle.classList.toggle('active');
            document.body.classList.toggle('menu-active');
            
            const isExpanded = menu.classList.contains('active');
            menuToggle.setAttribute('aria-expanded', isExpanded);
            
            // Atualizar ícone
            const icon = menuToggle.querySelector('i');
            icon.classList.replace(
                isExpanded ? 'fa-bars' : 'fa-times',
                isExpanded ? 'fa-times' : 'fa-bars'
            );
        });

        // Fechar menu ao clicar fora ou em um link
        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && !menuToggle.contains(e.target)) {
                closeMenu();
            }
        });

        // Fechar menu ao pressionar ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });

        // Fechar menu ao clicar em links
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // Função para fechar menu
        function closeMenu() {
            menu.classList.remove('active');
            menuToggle.classList.remove('active');
            document.body.classList.remove('menu-active');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.querySelector('i').classList.replace('fa-times', 'fa-bars');
        }
    }

    // Botão Voltar ao Topo com scroll suave
    const voltarTopo = document.querySelector('.voltar-topo');
    
    window.addEventListener('scroll', function() {
        const scrollPos = window.scrollY;
        if (scrollPos > 300) {
            voltarTopo?.classList.add('visible');
        } else {
            voltarTopo?.classList.remove('visible');
        }
    });

    voltarTopo?.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Loading states para botões
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('[type="submit"]');
            if (btn) {
                btn.classList.add('btn-loading');
                btn.innerHTML = 'Processando...';
            }
        });
    });

    // Tooltips personalizados
    document.querySelectorAll('[title]').forEach(el => {
        el.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = this.getAttribute('title');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = rect.bottom + 10 + 'px';
            tooltip.style.left = rect.left + (rect.width/2) - (tooltip.offsetWidth/2) + 'px';
            
            this.addEventListener('mouseleave', () => tooltip.remove());
        });
    });

    // Confirmações personalizadas
    window.confirmarAcao = function(mensagem, callback) {
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.innerHTML = `
            <div class="confirm-content">
                <p>${mensagem}</p>
                <div class="confirm-actions">
                    <button class="btn-confirm">Confirmar</button>
                    <button class="btn-cancel">Cancelar</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('.btn-confirm').onclick = () => {
            modal.remove();
            callback(true);
        };
        
        modal.querySelector('.btn-cancel').onclick = () => {
            modal.remove();
            callback(false);
        };
    };

    handleCookies();
    lazyLoadImages();
    handleScrollAnimations();
    handlePreloader();

    // Suporte para eventos touch
    if (window.matchMedia("(max-width: 768px)").matches) {
        document.addEventListener('touchstart', handleTouchStart, false);
        document.addEventListener('touchmove', handleTouchMove, false);

        let xDown = null;
        let yDown = null;

        function handleTouchStart(evt) {
            const firstTouch = evt.touches[0];
            xDown = firstTouch.clientX;
            yDown = firstTouch.clientY;
        }

        function handleTouchMove(evt) {
            if (!xDown || !yDown) return;

            const xUp = evt.touches[0].clientX;
            const yUp = evt.touches[0].clientY;
            const xDiff = xDown - xUp;
            const yDiff = yDown - yUp;

            if (Math.abs(xDiff) > Math.abs(yDiff)) {
                if (xDiff > 0 && menu.classList.contains('active')) {
                    // Deslizar para esquerda - fecha menu
                    closeMenu();
                }
            }

            xDown = null;
            yDown = null;
        }
    }

    // Ajustar height em mobile
    function setMobileHeight() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

    setMobileHeight();
    window.addEventListener('resize', setMobileHeight);
});

// LGPD Cookie Control
function handleCookies() {
    const banner = document.getElementById('lgpd-banner');
    const acceptBtn = document.getElementById('accept-cookies');
    
    if (localStorage.getItem('cookies-accepted') !== 'true') {
        banner.classList.add('show');
    }
    
    acceptBtn?.addEventListener('click', () => {
        localStorage.setItem('cookies-accepted', 'true');
        banner.classList.remove('show');
    });
}

// Lazy Loading de Imagens
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Animações na Rolagem
function handleScrollAnimations() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    
    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-up');
            }
        });
    }, { threshold: 0.1 });
    
    elements.forEach(el => scrollObserver.observe(el));
}

// Preloader
function handlePreloader() {
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        window.addEventListener('load', () => {
            preloader.classList.add('fade-out');
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        });
    }
}

// Botão "Copiar WhatsApp"
function copiarWhatsApp() {
    const numero = '71992124952';
    navigator.clipboard.writeText(numero).then(() => {
        const btn = document.querySelector('.copy-whatsapp');
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy"></i> Copiar WhatsApp';
        }, 2000);
    });
}
