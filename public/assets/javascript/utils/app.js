document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebarCloseButton = document.getElementById('sidebar-close-button'); // O botão DENTRO do sidebar
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function abrirMenu() {
        if (sidebar) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
        }
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('hidden');
            void sidebarOverlay.offsetWidth; // Força reflow para transição
            sidebarOverlay.classList.add('opacity-100');
        }
        // document.body.classList.add('overflow-hidden'); // Opcional: trava scroll do body
    }

    function fecharMenu() {
        if (sidebar) {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('opacity-100');
            setTimeout(() => { // Espera a opacidade sumir antes de esconder
                sidebarOverlay.classList.add('hidden');
            }, 300); // Duração da transição de opacidade
        }
        // document.body.classList.remove('overflow-hidden');
    }

    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function (event) {
            event.stopPropagation();
            abrirMenu();
        });
    }

    if (sidebarCloseButton) {
        sidebarCloseButton.addEventListener('click', function (event) {
            event.stopPropagation();
            fecharMenu();
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            fecharMenu();
        });
    }

    document.addEventListener('keydown', function (event) {
        // Fecha apenas se o menu estiver visível (translate-x-0) e a tela for pequena (onde o overlay/toggle é relevante)
        if (event.key === 'Escape' && sidebar && sidebar.classList.contains('translate-x-0') && window.innerWidth < 1024) { // 1024px é o breakpoint 'lg' do Tailwind
            fecharMenu();
        }

        
    });

    // Abre o menu sempre que a tela for redimensionada para um tamanho maior que o breakpoint 'lg'
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            if (sidebar) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.add('hidden');
                sidebarOverlay.classList.remove('opacity-100');
            }
            // document.body.classList.remove('overflow-hidden');
        } else {
            if (sidebar) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
            }
            // document.body.classList.remove('overflow-hidden');
        }
    });

    // Lógica de fechar alertas flash (o seu código jQuery aqui estava ok)
    document.querySelectorAll('#mensagem-flash').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = "opacity 0.5s ease";
            el.style.opacity = 0;
            setTimeout(() => el.remove(), 500); // remove após o fade-out
        }, 5000); // 10 segundos
    });

});


/** 
 * =================================
 * EVENTOS - SAIR SEM SALVAR
 * =================================
 */
